<?php

namespace EmizorIpx\ClientFel\Reports\Clients;

use EmizorIpx\ClientFel\Http\Resources\InvoiceReportResource;
use EmizorIpx\ClientFel\Reports\BaseReport;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\ClientReportResource;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use Exception;

class ClientsReport extends BaseReport implements ReportInterface {

    protected $type_document;

    protected $columns;

    protected $company_id;

    protected $user;


    public function __construct( $company_id, $request, $columns, $user )
    {
        $this->company_id = $company_id;

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


    public function addTypeDocumentFilter ( $query ) {

        if( !is_null( $this->type_document ) ) {

            return $query->where('fel_clients.type_document_id', $this->type_document);

        }

        return $query;

    }

    public function addSelectColumns( $query ) {

        foreach( $this->columns as $column) {

            if( in_array($column, ['email', 'type_document_id']) ){
                continue;
            }

            if( in_array( $column, ['business_name'] ) ){
                
                $query->addSelect("fel_clients.$column");

            } else {
                
                $query->addSelect("clients.$column");
            }
                
        }

        $query->selectRaw('(SELECT email FROM client_contacts WHERE client_id = clients.id LIMIT 1) as email');

        $query->selectRaw('(SELECT descripcion FROM fel_identity_document_types WHERE fel_identity_document_types.codigo = fel_clients.type_document_id) as type_document_id');

        $query->addSelect("fel_clients.document_number");
        $query->addSelect("fel_clients.complement");

        return $query;

    }


    public function generateReport() {

        $query_clients = \DB::table('clients')->join('fel_clients', 'fel_clients.id_origin', '=' , 'clients.id')->where('fel_clients.company_id', $this->company_id) ;

        $query_clients = $this->addDateFilter($query_clients);


        $query_clients = $this->addTypeDocumentFilter($query_clients);
        
        $query_clients =  $this->addSelectColumns($query_clients);


        // \Log::debug("SQL Statement: ". $query_clients->toSql());


        $clients = $query_clients->get();

        \Log::debug("Columns to Select: "  . json_encode($clients));

        // dd($clients);

        return [
            "header" => [
                "sucursal" => is_null($this->branch_code) ?  "Todos" : ($this->branch_code == 0 ? "Casa Matriz" : 'Sucursal ' . $this->branch_code),
                "usuario" => $this->user->name(),
                "fechaReporte" => Carbon::now()->timezone('America/La_Paz')->toDateTimeString()
            ],
            "clients" => ClientReportResource::collection($clients)->resolve()
        ];

    }

}
