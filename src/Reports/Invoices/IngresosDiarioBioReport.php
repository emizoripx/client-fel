<?php

namespace EmizorIpx\ClientFel\Reports\Invoices;

use App\Models\User;
use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\InvoicesIngresosDiarioBioResource;

class IngresosDiarioBioReport extends BaseReport implements ReportInterface {

    protected $branch_code;

    protected $company_id;

    protected $user;

    protected $branch_desc = 'Todos';

    public function __construct( $company_id, $request, $columns, $user, $headers )
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch_code') ? $request->get('branch_code') : null;

        $from = $request->has('from_date') ? $request->get('from_date') : null;
        $to = $request->has('to_date') ? $request->get('to_date') : null;
        
        $this->user = $user;

        parent::__construct($from, $to);
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

    public function generateReport(){

        $query_invoices = \DB::table('invoices')->join('fel_invoice_requests', 'fel_invoice_requests.id_origin', '=' , 'invoices.id')
                                ->join('fel_payment_methods', 'fel_invoice_requests.codigoMetodoPago', 'fel_payment_methods.codigo')
                                ->leftJoin('paymentables', 'paymentables.paymentable_id', 'invoices.id')
                                ->leftJoin('payments', 'payments.id', 'paymentables.payment_id')
                                ->leftJoin('payment_types', 'payments.type_id', 'payment_types.id')
                                ->where('fel_invoice_requests.company_id', $this->company_id)
                                ->where('fel_invoice_requests.codigoEstado', 690) ;

        if ($this->user && ! $this->user->hasPermission('view_invoice')) {

            \Log::debug("Filter By User: " . $this->user->id);

            $query_invoices = $query_invoices->where('invoices.user_id', '=', $this->user->id);
        }

        $query_invoices = $this->addDateFilter($query_invoices);

        $query_invoices = $this->addBranchFilter($query_invoices);

        $invoices = $query_invoices->select(
            'fechaEmision', 'fel_invoice_requests.nombreRazonSocial', 'fel_invoice_requests.montoTotal', 'invoices.paid_to_date as montoPagado', 'fel_invoice_requests.numeroFactura'
        )
        ->selectRaw('JSON_EXTRACT(extras, "$.orders") as codigoOrden')
        ->selectRaw('JSON_EXTRACT(extras, "$.business") as empresa')
        ->selectRaw('IF( invoices.paid_to_date > 0, payment_types.`name`, "POR COBRAR" ) AS tipoPago')
        ->get();

        // \Log::debug("Invoices: " . json_encode($invoices));

        // dd($totals_payments);

        return [
            'header' => [
                'sucursal' => $this->branch_desc,
                'usuario' => $this->user->name(),
                'fechaReporte' => Carbon::now()->toDateTimeString(),
            ],
            'totales' => [
                'montoTotalGeneral' => collect($invoices)->sum('montoTotal')
            ],
            'invoices' => InvoicesIngresosDiarioBioResource::collection($invoices)->resolve()
        ];

    }

}