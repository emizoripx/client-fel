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

        $query_base = \DB::table('fel_invoice_requests')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->where(function ($query) {
                $query->whereNull('fel_invoice_requests.codigoEstado')
                ->orWhere('fel_invoice_requests.codigoEstado', '!=', 902);
            });

        $query_base = $this->addDateFilter($query_base);
        $query_base = $query_base->select('fel_invoice_requests.id', 'fel_invoice_requests.numeroFactura', 'fel_invoice_requests.fechaEmision');


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
            ->leftJoin("group_settings", 'group_settings.id', 'clients.group_settings_id')
            ->whereNotNull('fel_invoice_requests.estado');

        $query_invoices =  $this->addSelectColumns($query_invoices);
        return [
            "header" => $this->headers_csv,
            "invoices" => $query_invoices
        ];
    }
}
