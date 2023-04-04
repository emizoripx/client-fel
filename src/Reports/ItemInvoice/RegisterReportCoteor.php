<?php

namespace EmizorIpx\ClientFel\Reports\ItemInvoice;

use EmizorIpx\ClientFel\Http\Resources\RegisterReportCoteorResource;
use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;

class RegisterReportCoteor extends BaseReport implements ReportInterface
{

    protected $branch_code;

    protected $type_document;

    protected $company_id;


    public function __construct($company_id, $request)
    {
        $this->company_id = $company_id;

        $this->type_document = $request->has('type_document') ? $request->get('type_document') : null;

        $from = $request->has('from_date') ? $request->get('from_date') : null;
        $to = $request->has('to_date') ? $request->get('to_date') : null;

        parent::__construct($from, $to);
    }
    

    public function addSelectColumns($query)
    {
        return $query->selectRaw('(@counter := @counter +1) as num, codigoCliente,cuf, nombreRazonSocial, numeroFactura, detalles, montoTotal, date_format( fechaEmision , "%d/%m/%Y" ) as fecha, concat(users.first_name, " ", users.last_name) as nombreUsuario , if(codigoEstado=690 ||  codigoEstado=908,"VALIDA","ANULADA") as estado, invoices.public_notes, invoices.private_notes, numeroDocumento');
    }

    public function generateReport()
    {
        \DB::statement(\DB::raw("set @counter := 0"));
        $query_invoices = \DB::table('fel_invoice_requests')
                        ->leftJoin('users','users.id','fel_invoice_requests.emitted_by')
                        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
                        ->where('fel_invoice_requests.company_id', $this->company_id)
                        ->whereNotNull('fel_invoice_requests.codigoEstado')
                        ->whereNotNull('fel_invoice_requests.cuf');
        $query_invoices = $this->addDateFilter($query_invoices);
        
        $query_invoices =  $this->addSelectColumns($query_invoices);
        
        $detalles = $query_invoices->pluck('fel_invoice_requests.detalles', 'fel_invoice_requests.cuf');
        \DB::statement(\DB::raw("set @counter := 0"));
        $invoices_grouped = collect($query_invoices->get())->groupBy('cuf');
        
        $items = collect($detalles)->map(function ($detail, $key) use ($invoices_grouped) {

                $invoice_data = json_decode(json_encode($invoices_grouped[$key]), true);
    
                $detail = json_decode($detail, true);
    
                $joined = collect($invoice_data)->crossJoin($detail)->all();
    
                $detalle = collect($joined)->map(function ($d) {
    
                    $merged = array_merge(...collect($d)->toArray());
    
                    return $merged;
                })->all();

                return $detalle;

        })->values();

        $items = ExportUtils::flatten_array($items);
        
        return [
            "header" => [
                "Nº",
                "Código Cliente",
                "Número de Documento",
                "Nombre Razón Social",
                "Nro Factura",
                "SERVICIO/PRODUCTO",
                "Monto Total",
                "Fecha Emisión",
                "Emitido Por",
                "Estado SIN",
                "CUF",
                "Nota Pública",
                "Nota Privada",
            ],
            "invoices" => RegisterReportCoteorResource::collection($items)->resolve()
        ];
    }
}
