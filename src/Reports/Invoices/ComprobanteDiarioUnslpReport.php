<?php

namespace EmizorIpx\ClientFel\Reports\Invoices;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;

class ComprobanteDiarioUnslpReport extends BaseReport implements ReportInterface
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
        $query->selectRaw('fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, group_settings.name,fel_invoice_requests.nombreEstudiante,fel_invoice_requests.detalles');
        
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
            "header" => [
                "Fecha",
                "Nro_Factura",
                "Carrera",
                "Alumno",
                "Matricula",
                "Mensualidad",
                "OtrosIngresos",
            ],
            "invoices" => $query_invoices
        ];
    }
}
