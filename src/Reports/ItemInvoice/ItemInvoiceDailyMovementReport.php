<?php

namespace EmizorIpx\ClientFel\Reports\ItemInvoice;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\ItemInvoiceDailyMovementResource;
use Hashids\Hashids;
class ItemInvoiceDailyMovementReport extends BaseReport implements ReportInterface {

    protected $branch_code;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $branch_desc = "Todos";

    protected $all_users;
    
    protected $user_selected;

    public function __construct( $company_id, $request, $columns, $user )
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch') ? $request->get('branch') : null;

        $this->from = $request->has('from_date') ? $request->get('from_date') : null;
        $this->to = $request->has('to_date') ? $request->get('to_date') : null;

        $this->user_selected = null;

        $this->all_users = $request->has('all_users') ? ($request->get('all_users') == "true" ? true : false) : false;
        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        
        $this->user = $user;
        if (!$this->all_users) {

            $user_selected = $request->has('user') ? ( !empty($request->get('user')) ? $hashid->decode( $request->get('user') ) : null) : null;
            $cu = null;
            if (!is_null($user_selected)) {
                $cu = \App\Models\CompanyUser::whereUserId($user_selected)->whereCompanyId($company_id)->first();
                \Log::debug("user selected ID = " , $user_selected);
            }
            if (!empty($cu) && !is_null($cu)) {
                $this->user_selected = $cu;
            }
        }

        $this->columns = $columns;

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

        $from = date('Y-m-d', $this->from_date) . " 00:00:00";
        $to = date("Y-m-d", $this->to_date) . " 23:59:59";

        $emitted = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->leftJoin('paymentables', 'paymentables.paymentable_id', 'invoices.id')
        ->leftJoin('payments', 'payments.id', 'paymentables.payment_id')
        ->leftJoin('payment_types', 'payments.type_id', 'payment_types.id')
        ->where('fel_invoice_requests.company_id', $this->company_id)
        ->whereNotNull('fel_invoice_requests.cuf')
        ->whereBetween('fel_invoice_requests.fechaEmision', [$from, $to])
        ->whereNotBetween('paymentables.created_at', [$from, $to])
        ->selectRaw(\DB::raw('fel_invoice_requests.id, fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", if(paymentables.created_at is null,"Por cobrar","PAGADO") ) AS estado, fel_invoice_requests.codigoCliente,fel_invoice_requests.numeroDocumento, fel_invoice_requests.nombreRazonSocial, payment_types.name as tipoPago, paymentables.created_at as fechaPago, fel_invoice_requests.detalles, fel_invoice_requests.usuario,fel_invoice_requests.montoTotal, JSON_EXTRACT(extras,"$.poliza") as poliza, JSON_EXTRACT(extras,"$.agencia") as agencia'));

        $emittend_payed = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->leftJoin('paymentables', 'paymentables.paymentable_id', 'invoices.id')
        ->leftJoin('payments', 'payments.id', 'paymentables.payment_id')
        ->leftJoin('payment_types', 'payments.type_id', 'payment_types.id')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf')
            ->whereBetween('fel_invoice_requests.fechaEmision', [$from, $to])
            ->whereBetween('paymentables.created_at', [$from, $to])
            ->selectRaw(\DB::raw('fel_invoice_requests.id, fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", if(paymentables.created_at is null,"Por cobrar","PAGADO") ) AS estado, fel_invoice_requests.codigoCliente,fel_invoice_requests.numeroDocumento, fel_invoice_requests.nombreRazonSocial, payment_types.name as tipoPago, paymentables.created_at as fechaPago, fel_invoice_requests.detalles, fel_invoice_requests.usuario,fel_invoice_requests.montoTotal, JSON_EXTRACT(extras,"$.poliza") as poliza, JSON_EXTRACT(extras,"$.agencia") as agencia'));

        $payed = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->leftJoin('paymentables', 'paymentables.paymentable_id', 'invoices.id')
        ->leftJoin('payments', 'payments.id', 'paymentables.payment_id')
        ->leftJoin('payment_types', 'payments.type_id', 'payment_types.id')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf')
            ->whereNotBetween('fel_invoice_requests.fechaEmision', [$from, $to])
            ->whereBetween('paymentables.created_at', [$from, $to])
            ->selectRaw(\DB::raw('fel_invoice_requests.id, fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", if(paymentables.created_at is null,"Por cobrar","PAGADO") ) AS estado, fel_invoice_requests.codigoCliente,fel_invoice_requests.numeroDocumento, fel_invoice_requests.nombreRazonSocial, payment_types.name as tipoPago, paymentables.created_at as fechaPago, fel_invoice_requests.detalles, fel_invoice_requests.usuario,fel_invoice_requests.montoTotal, JSON_EXTRACT(extras,"$.poliza") as poliza, JSON_EXTRACT(extras,"$.agencia") as agencia'));

        $debts = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->leftJoin('paymentables', 'paymentables.paymentable_id', 'invoices.id')
        ->leftJoin('payments', 'payments.id', 'paymentables.payment_id')
        ->leftJoin('payment_types', 'payments.type_id', 'payment_types.id')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf')
            ->whereBetween('fel_invoice_requests.fechaEmision', [$from, $to])
            ->whereNull('paymentables.created_at')
            ->selectRaw(\DB::raw('fel_invoice_requests.id, fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", if(paymentables.created_at is null,"Por cobrar","PAGADO") ) AS estado, fel_invoice_requests.codigoCliente,fel_invoice_requests.numeroDocumento, fel_invoice_requests.nombreRazonSocial, payment_types.name as tipoPago, paymentables.created_at as fechaPago, fel_invoice_requests.detalles, fel_invoice_requests.usuario,fel_invoice_requests.montoTotal, JSON_EXTRACT(extras,"$.poliza") as poliza, JSON_EXTRACT(extras,"$.agencia") as agencia'));
        $emitted = $this->addBranchFilter($emitted);
        $emittend_payed = $this->addBranchFilter($emittend_payed);
        $payed = $this->addBranchFilter($payed);
        $debts = $this->addBranchFilter($debts);
        \Log::debug("ALL USERS STATUS VARIABLE : " . $this->all_users);
        if (!$this->all_users) {
            
            if (!is_null($this->user_selected)) {
                \Log::debug("using user select _id : " . $this->user_selected->user_id);
                $emitted = $emitted->where('invoices.user_id', '=', $this->user_selected->user_id);
                $emittend_payed = $emittend_payed->where('invoices.user_id', '=', $this->user_selected->user_id);
                $payed = $payed->where('invoices.user_id', '=', $this->user_selected->user_id);
                $debts = $debts->where('invoices.user_id', '=', $this->user_selected->user_id);
            }else if ($this->user && ! $this->user->hasPermission('view_invoice')) {
                
                \Log::debug("Filter By User: " . $this->user->id);
                
                $emitted = $emitted->where('invoices.user_id', '=', $this->user->id);
                $emittend_payed = $emittend_payed->where('invoices.user_id', '=', $this->user->id);
                $payed = $payed->where('invoices.user_id', '=', $this->user->id);
                $debts = $debts->where('invoices.user_id', '=', $this->user->id);

            }
        }
        $query_items = $debts->union($emitted)
            ->union($emittend_payed)
            ->union($payed)
            ->get();   

        $detalles = $query_items->pluck('detalles', 'id');

        $invoices_grouped = collect($query_items)->groupBy('id');
        $dictionary_payment_types = ExportUtils::dictionaryPaymentTypesSpanish(); 
        $items = collect( $detalles )->map( function( $detail, $key ) use ($invoices_grouped, $dictionary_payment_types) {

            $invoice_data = json_decode(json_encode($invoices_grouped[$key]), true) ;
            
            $detail = json_decode($detail, true);
            
            $invoice_data[0]['tipoPago'] = $dictionary_payment_types[$invoice_data[0]['tipoPago']];
            $joined = collect($invoice_data)->crossJoin($detail)->all();

            $detalle = collect($joined)->map( function ( $d ) {

                $merged = array_merge(...collect($d)->toArray());

                return $merged;

            })->all();


            return $detalle;

        })->values();

        $items = ExportUtils::flatten_array($items);
        
        $totales = [];
        $not_payed = 0.00;
        collect($items)->groupBy('tipoPago')->map(function ($item, $key) use (&$totales, &$not_payed) {
            if ($key != "")
                $totales[] = ["name" => $key, "monto" => $item->sum('subTotal')];
            $not_payed = (float)$not_payed + (float)$item->where('estado', 'Por cobrar')->sum('subTotal');
        });
        $totales[] = ["name"=>"Por cobrar", "monto"=> $not_payed];

        return [
            "header" => [
                "nit" => \App\Models\Company::find($this->company_id)->settings->id_number,
                "desde" => date('Y-m-d', $this->from) . " 00:00:00",
                "hasta" => date('Y-m-d', $this->to) . " 23:59:59",
                "fechaReporte" => Carbon::now()->format("Y-m-d"),
                "usuario" => $this->user->name(),
                "sucursal" => $this->branch_desc,
                "nombreUsuario" => $this->all_users || is_null($this->user_selected) ? "TODOS": $this->user_selected->user->name()
            ],
            "totales" => $totales,
            "items" => ItemInvoiceDailyMovementResource::collection($items)->resolve()
        ];
        
    }

}