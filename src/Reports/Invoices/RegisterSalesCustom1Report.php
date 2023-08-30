<?php

namespace EmizorIpx\ClientFel\Reports\Invoices;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;

class RegisterSalesCustom1Report extends BaseReport implements ReportInterface
{

    protected $branch_code;

    protected $type_document;

    protected $company_id;

    protected $revocated_zero;


    public function __construct($company_id, $request)
    {
        $this->company_id = $company_id;

        $this->type_document = $request->has('type_document') ? $request->get('type_document') : null;

        $this->revocated_zero = $request->has('revocated_zero') ? $request->get('revocated_zero') : false;
        $from = $request->has('from_date') ? $request->get('from_date') : null;
        $to = $request->has('to_date') ? $request->get('to_date') : null;

        parent::__construct($from, $to);
    }

    public function addSelectColumns($query)
    {
  
        $query->selectRaw('numeroDocumento,nombreRazonSocial,fechaEmision, round(montoTotal,2) as subTotal, if(codigoEstado=690 ||  codigoEstado=908,"V","A") as estado, cuf, concat(invoices.private_notes,"/",invoices.public_notes)');
        
        return $query;
    }

    public function generateReport()
    {
        $query_invoices = \DB::table('fel_invoice_requests')
                        ->leftJoin('invoices','invoices.id','fel_invoice_requests.id_origin')
                        ->where('fel_invoice_requests.company_id', $this->company_id)
                        ->whereNotNull('fel_invoice_requests.codigoEstado')
                        ->whereNotNull('fel_invoice_requests.cuf');   
        $query_invoices = $this->addDateFilter($query_invoices);
        $query_invoices =  $this->addSelectColumns($query_invoices);


        return [
            "header" => [
                "NIT / CI CLIENTE",
                "NOMBRE O RAZON SOCIAL",
                "FECHA DE LA FACTURA",
                "SUBTOTAL", 
                "ESTADO",
                "CODIGO DE AUTORIZACION",
                "NOTAS",
            ],
            "invoices" => $query_invoices
        ];
    }
}
