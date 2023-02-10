<?php

namespace EmizorIpx\ClientFel\Reports\ItemInvoice;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\ItemInvoiceDailyMovementResource;

class ItemInvoiceDailyReport extends BaseReport implements ReportInterface {

    protected $branch_code;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $branch_desc = "Todos";

    protected $same_user;
    
    protected $paid_range_filter;

    public function __construct( $company_id, $request, $columns, $user )
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch') ? $request->get('branch') : null;

        $this->from = $request->has('from_date') ? $request->get('from_date') : null;
        $this->to = $request->has('to_date') ? $request->get('to_date') : null;
        
        $this->columns = $columns;

        $this->same_user = $request->has('same_user') ? ($request->get('same_user') == "true" ? true:false) : false;
        $this->paid_range_filter = $request->has('paid_range_filter') ? ($request->get('paid_range_filter') == "true" ? true:false) : false;

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

        $query_items = \DB::table('fel_invoice_requests')
                        ->leftJoin('invoices','invoices.id','fel_invoice_requests.id_origin')
                        ->leftJoin('paymentables', 'paymentables.paymentable_id','invoices.id')
                        ->leftJoin('payments', 'payments.id', 'paymentables.payment_id')
                        ->leftJoin('payment_types', 'payments.type_id','payment_types.id')
                        ->where('fel_invoice_requests.company_id', $this->company_id)
                        ->whereNotNull('fel_invoice_requests.cuf');

        if ($this->same_user) {
            $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
        }else {
            if ($this->user && !$this->user->hasPermission('view_invoice')) {

                \Log::debug("Filter By User: " . $this->user->id);

                $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
            }
        }
        
        $query_items = $this->addBranchFilter($query_items);
        if (!$this->paid_range_filter){

            $query_items = $this->addDateFilter($query_items);

        }else {
            $from = date('Y-m-d', $this->from) . " 00:00:00";
            $to = date("Y-m-d", $this->to) . " 23:59:59";

            $query_items = $query_items->whereBetween('paymentables.created_at', [$from, $to]);
        }

        $detalles = $query_items->pluck('fel_invoice_requests.detalles', 'fel_invoice_requests.id');

        $invoices = $query_items->selectRaw(\DB::raw('fel_invoice_requests.id, codigoEstado,fel_invoice_requests.descuentoAdicional, fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", if(paymentables.created_at is null,"Por cobrar","PAGADO") ) AS estado, fel_invoice_requests.codigoCliente,fel_invoice_requests.numeroDocumento, fel_invoice_requests.nombreRazonSocial, payment_types.name as tipoPago, paymentables.created_at as fechaPago, fel_invoice_requests.detalles, fel_invoice_requests.usuario,fel_invoice_requests.montoTotal, JSON_EXTRACT(extras,"$.poliza") as poliza, JSON_EXTRACT(extras,"$.agencia") as agencia'))->get();

        $invoices_grouped = collect($invoices)->groupBy('id');
        
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

        $invoice_date = null;
        $invoice_number = null;
        $tipoPago = null;

        $items_changed = collect($items)->map(function ($item, $key) use (&$invoice_date, &$invoice_number, &$tipoPago) {

            if (in_array($item['codigoEstado'], [905, 691])) {
                $item['montoTotal'] = 0;
            }

            if (($invoice_date == $item['fechaEmision']) && ($invoice_number == $item['numeroFactura'])) {

                $item['montoTotal'] = 0;
                $item['estado'] = "";
                $item['numeroFactura'] = "";
                $item['fechaEmision'] = "";
                $item['codigoEstado'] = "";
                $item['descuentoAdicional'] = "";
                if ($tipoPago == $item['tipoPago']) {
                    $item['tipoPago'] = "";
                    $item['fechaPago'] = "";
                }
                $tipoPago = $item['tipoPago'];
                $item['usuario'] = "";
            } else {

                $invoice_date = $item['fechaEmision'];

                $invoice_number = $item['numeroFactura'];

                $tipoPago = $item['tipoPago'];
            }

            return $item;
        });
        $totales = [];
        $not_payed = 0.00;
        collect($items_changed)->groupBy('tipoPago')->map(function ($item, $key) use (&$totales, &$not_payed) {
            if ($key != "")
                $totales[] = ["name" => $key, "monto" => $item->sum('montoTotal')];
            $not_payed = (float)$not_payed + (float)$item->where('estado', 'Por cobrar')->sum('montoTotal');
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
            ],
            "totales" => $totales,
            "items" => ItemInvoiceDailyMovementResource::collection($items_changed)->resolve()
        ];
        
    }

}