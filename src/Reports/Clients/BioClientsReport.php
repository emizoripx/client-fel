<?php

namespace EmizorIpx\ClientFel\Reports\Clients;

use EmizorIpx\ClientFel\Http\Resources\InvoiceReportResource;
use EmizorIpx\ClientFel\Reports\BaseReport;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\BioClientReportResource;
use EmizorIpx\ClientFel\Http\Resources\ClientReportResource;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use Exception;

class BioClientsReport extends BaseReport implements ReportInterface {

    protected $type_document;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $headers;

    public function __construct( $company_id, $request, $columns, $user, $headers = [] )
    {
        $this->company_id = $company_id;

        $this->headers = $headers;

        $this->branch_code = $request->has('branch_code') ? $request->get('branch_code') : null;

        $this->type_document = $request->has('type_document') ? $request->get('type_document') : null;

        $this->state = $request->has('state') ? $request->get('state') : null;

        $this->group = $request->has('group') ? $request->get('group') : null;

        $from = $request->has('from_date') ? $request->get('from_date') : null;
        $to = $request->has('to_date') ? $request->get('to_date') : null;

        $this->columns = $columns;

        $this->user = $user;

        parent::__construct($from, $to);
        
    }

    public function addDateFilter( $query ) {

        if(!is_null($this->from_date) && !is_null($this->to_date)){

            $from = date('Y-m-d', $this->from_date)." 00:00:00";
            $to = date("Y-m-d", $this->to_date). " 23:59:59";
            \Log::debug("From Date: " . $from);
            \Log::debug("To Date: " . $to);

            return $query->whereBetween('clients.created_at', [$from, $to]);
        } else {
            return $query;
        }

    }


    public function generateReport() {

        $query_clients = \DB::table('clients')->join('fel_clients', 'fel_clients.id_origin', '=' , 'clients.id')
                                                ->join( \DB::raw("(SELECT * FROM client_contacts WHERE id IN ( SELECT MIN(id) FROM client_contacts WHERE company_id = {$this->company_id} GROUP BY client_id )) AS contacts"), 'clients.id', '=', 'contacts.client_id')
                                                ->where('fel_clients.company_id', $this->company_id) ;

        $query_clients = $this->addDateFilter($query_clients);

        $query_clients->select('clients.number', 'clients.name', 'fel_clients.business_name','fel_clients.type_document_id', 'fel_clients.document_number', 'fel_clients.complement', 'contacts.phone', 'contacts.email','contacts.contact_key', 'clients.created_at'); 
        // \Log::debug("SQL Statement: ". $query_clients->toSql());


        $clients = $query_clients->get();

        \Log::debug("Columns to Select: "  . json_encode($clients));

        // dd($clients);

        return [
            "header" => [
                "sucursal" => is_null($this->branch_code) ?  "Todos" : ($this->branch_code == 0 ? "Casa Matriz" : 'Sucursal ' . $this->branch_code),
                "usuario" => $this->user->name(),
                "fechaReporte" => Carbon::now()->toDateTimeString()
            ],
            "clients" => BioClientReportResource::collection($clients)->resolve()
        ];

    }

}
