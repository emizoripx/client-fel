<?php

namespace EmizorIpx\ClientFel\Reports\Invoices;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;

class ComprobanteDiarioUnslpReport extends BaseReport implements ReportInterface
{

    protected $branch_code;

    protected $type_document;

    protected $company_id;

    protected $headers_csv;


    public function __construct($company_id, $request, $columns, $user, $headers_csv)
    {
        $this->company_id = $company_id;

        $this->type_document = $request->has('type_document') ? $request->get('type_document') : null;

        $from = $request->has('from_date') ? $request->get('from_date') : null;
        $to = $request->has('to_date') ? $request->get('to_date') : null;
        $this->headers_csv = $headers_csv;
        parent::__construct($from, $to);
    }

    public function addSelectColumns($query)
    {
        $query->selectRaw('fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, group_settings.name,clients.name as clientname,fel_invoice_requests.detalles');
        
        return $query;
    }

    public function generateReport()
    {
        
        $query_invoices = \DB::table('fel_invoice_requests')
                        ->leftJoin("invoices",'fel_invoice_requests.id_origin','invoices.id')
                        ->leftJoin("clients",'invoices.client_id','clients.id')
                        ->leftJoin("group_settings", 'group_settings.id', 'clients.group_settings_id')
                        ->where('fel_invoice_requests.company_id', $this->company_id)
                        ->whereNotNull('fel_invoice_requests.codigoEstado')  
                        ->whereNotNull('fel_invoice_requests.cuf');  

        $query_invoices = $this->addDateFilter($query_invoices);
        $query_invoices =  $this->addSelectColumns($query_invoices);

      

        return [
            "header" => $this->headers_csv,
            "invoices" => $query_invoices
        ];
    }
}
