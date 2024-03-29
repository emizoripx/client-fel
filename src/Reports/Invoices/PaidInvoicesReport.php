<?php

namespace EmizorIpx\ClientFel\Reports\Invoices;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\PaidInvoicesResource;
class PaidInvoicesReport extends BaseReport implements ReportInterface {

    protected $branch_code;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $branch_desc = "Todos";

    public function __construct( $company_id, $request, $columns, $user )
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch') ? $request->get('branch') : null;

        $this->from = $request->has('from_date') ? $request->get('from_date') : null;
        $this->to = $request->has('to_date') ? $request->get('to_date') : null;
        
        $this->columns = $columns;

        $this->user = $user;

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
        ->leftJoin('paymentables', 'paymentables.paymentable_id', 'invoices.id')
        ->leftJoin('payments', 'payments.id', 'paymentables.payment_id')
        ->leftJoin('payment_types', 'payments.type_id', 'payment_types.id')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf')
            ->whereIn('invoices.status_id',[3,4]);

        $query_items = $this->addBranchFilter($query_items);
        $query_items = $this->addDateFilter($query_items);


        $invoices = $query_items->selectRaw(\DB::raw(
            '
            fel_invoice_requests.numeroFactura, 
            fel_invoice_requests.fechaEmision,
            fel_invoice_requests.cuf, 
            fel_invoice_requests.codigoSucursal, 
            payments.amount,
            fel_invoice_requests.estado,
            fel_invoice_requests.codigoEstado,
            invoices.status_id,
            invoices.client_id,
            fel_invoice_requests.codigoCliente,
            payment_types.name as tipoPago,
            payments.transaction_reference,
            fel_invoice_requests.nombreRazonSocial
         '
        ))->get();

        //ensure only valid, include offline invoices
        $invoices = $invoices->filter(function ($item) {
            return !is_null($item->estado) && (is_null($item->codigoEstado) ||  ($item->codigoEstado != 902 && $item->codigoEstado != 691));
        })->values();

        $invoices = $invoices->map(function ($item) {
            $item->tipoPago = ctrans('texts.payment_type_' . $item->tipoPago);
            return $item;
        });

        return [
            "header" => [
                "nit" => \App\Models\Company::find($this->company_id)->settings->id_number,
                "desde" => date('Y-m-d', $this->from_date) . " 00:00:00",
                "hasta" => date('Y-m-d', $this->to_date) . " 23:59:59",
                "fechaReporte" => Carbon::now()->timezone('America/La_Paz')->format("Y-m-d"),
                "usuario" => $this->user->name(),
                "sucursal" => $this->branch_desc
            ],
            "totales" => ["total" => collect($invoices)->sum("amount")],
            "items" => PaidInvoicesResource::collection($invoices)->resolve(),
        ];

    }

}
