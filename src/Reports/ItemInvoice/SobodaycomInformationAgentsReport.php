<?php

namespace EmizorIpx\ClientFel\Reports\ItemInvoice;

use EmizorIpx\ClientFel\Http\Resources\RegisterReportCoteorResource;
use EmizorIpx\ClientFel\Http\Resources\SobodaycomInformationAgentsResource;
use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;

class SobodaycomInformationAgentsReport extends BaseReport implements ReportInterface
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
        return $query->selectRaw('(@counter := @counter +1) as num,cuf, nombreRazonSocial, numeroFactura, codigoSucursal, codigoPuntoVenta, montoTotal ,  numeroDocumento, extras , clients.name as client_name');
    }

    public function generateReport()
    {

        \DB::statement(\DB::raw("set @counter := 0"));

        $query_base = \DB::table('fel_invoice_requests')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->where(function ($query) {
                $query->whereNull('fel_invoice_requests.codigoEstado')
                ->orWhere('fel_invoice_requests.codigoEstado', '!=', 902);
            });

        $query_base = $this->addDateFilter($query_base);
        $query_base = $query_base->select('fel_invoice_requests.id', 'fel_invoice_requests.numeroFactura', 'fel_invoice_requests.fechaEmision');

        $query_invoices = \DB::table('fel_invoice_requests')->select('fel_invoice_requests.id');
        $query_invoices = $query_invoices
            ->mergeBindings($query_base)
            ->join(
                \DB::raw('(' . $query_base->toSql() . ') pr'),
                function ($join) {
                    $join->on('fel_invoice_requests.id', '=', 'pr.id');
                }
            )
            ->leftJoin("invoices", 'fel_invoice_requests.id_origin', 'invoices.id')
            ->leftJoin("clients", 'invoices.client_id', 'clients.id')
            ->whereNotNull('fel_invoice_requests.estado');

        $query_invoices =  $this->addSelectColumns($query_invoices);
        $query_invoices = $query_invoices->get();
        
        return [
            "header" => [
                "Nº",
                "Nro Factura",
                "Nro Autorización",
                "Número de Documento",
                "Nombre Razón Social",
                "Evento Rubro",
                "Lugar de evento",
                "Fecha evento",
                "Artista ó grupos musicales",
                "Importe total",
            ],
            "invoices" => SobodaycomInformationAgentsResource::collection($query_invoices)->resolve()
        ];
    }
}
