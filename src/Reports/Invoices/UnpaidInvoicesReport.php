<?php

namespace EmizorIpx\ClientFel\Reports\Invoices;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\UnpaidInvoicesResource;
use App\Utils\Traits\MakesHash;
class UnpaidInvoicesReport extends BaseReport implements ReportInterface {

    protected $branch_code;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $branch_desc = "Todos";

    protected $client_id;

    public function __construct( $company_id, $request, $columns, $user )
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch') ? $request->get('branch') : null;

        $this->from = $request->has('from_date') ? $request->get('from_date') : null;
        $this->to = $request->has('to_date') ? $request->get('to_date') : null;
        
        $this->columns = $columns;

        $this->user = $user;

        $this->client_id = $request->has('client') ? $this->decodePrimaryKey($request->get('client'))  : null;

        parent::__construct($this->from, $this->to);
        
    }

    public function addBranchFilter( $query ) {

        if( !is_null($this->branch_code) ) {

            \Log::debug("Filter by Brach: " . $this->branch_code);

            $this->branch_desc = "Sucursal " . $this->branch_code;

            return $query->where('fel_invoice_requests.codigoSucursal', $this->branch_code);

        } elseif( count($branch_access = $this->user->getOnlyBranchAccess()) > 0 ) {

            $branch_access = $this->user->getOnlyBranchAccess();

            \Log::debug("Filter by Access Branch");

            $branches_desc = [];
            foreach ($branch_access as $value) {
                array_push( $branches_desc, ($value == 0 ? " Casa Matriz" : " Sucursal " . $value) );  
            }

            $this->branch_desc = implode(" - ", $branches_desc);

            return $query->whereIn('fel_invoice_requests.codigoSucursal', $branch_access);

        }

        return $query;
    }


    public function generateReport()
    {
        ini_set('memory_limit', '512M');
        
        $query_items = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf')
            ->where('invoices.status_id', '<', 4)
            ->where('invoices.client_id', $this->`client_id`);

        $client = \App\Models\Client::find($this->client_id);
        $client_name = "S/N";
        if (!empty($client)) {
            $client_name = $client->name;
        }

        $query_items = $this->addBranchFilter($query_items);
        $query_items = $this->addDateFilter($query_items);


        $invoices = $query_items->selectRaw(\DB::raw(
            '
            fel_invoice_requests.numeroFactura, 
            fel_invoice_requests.fechaEmision,
            fel_invoice_requests.cuf, 
            fel_invoice_requests.montoTotal,
            fel_invoice_requests.estado,
            fel_invoice_requests.codigoEstado,
            invoices.status_id,
            invoices.balance,
            invoices.client_id,
            fel_invoice_requests.nombreRazonSocial,
            fel_invoice_requests.numeroDocumento
         '
        ))->get();

        //ensure only valid, include offline invoices
        $invoices = $invoices->filter(function ($item) {
            return !is_null($item->estado) && (is_null($item->codigoEstado) ||  ($item->codigoEstado != 902 && $item->codigoEstado != 691));
        })->values();

        return [
            "header" => [
                "nit" => \App\Models\Company::find($this->company_id)->settings->id_number,
                "desde" => date('Y-m-d', $this->from_date) . " 00:00:00",
                "hasta" => date('Y-m-d', $this->to_date) . " 23:59:59",
                "fechaReporte" => Carbon::now()->timezone('America/La_Paz')->format("Y-m-d"),
                "usuario" => $this->user->name(),
                "sucursal" => $this->branch_desc,
                "nombreCliente" => $client_name,
            ],
            "totales" => ["total" => collect($invoices)->sum("montoTotal"), "balance" => collect($invoices)->sum("balance")],
            "items" => UnpaidInvoicesResource::collection($invoices)->resolve(),
        ];

    }

}
