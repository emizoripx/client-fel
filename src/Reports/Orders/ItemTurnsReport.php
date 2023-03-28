<?php

namespace EmizorIpx\ClientFel\Reports\Orders;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\ItemInvoiceResource;
use EmizorIpx\ClientFel\Utils\Documents;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class ItemTurnsReport extends BaseReport implements ReportInterface {

    protected $branch_code;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $branch_desc = "Todos";

    public function __construct( $company_id, $request, $columns, $user )
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch_code') ? $request->get('branch_code') : null;

        $from = $request->has('from_date') ? $request->get('from_date') : null;
        $to = $request->has('to_date') ? $request->get('to_date') : null;

        $this->columns = $columns;

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

    public function addDateFilter( $query ) {

        if(!is_null($this->from_date) && !is_null($this->to_date)){

            $from = date('Y-m-d', $this->from_date)." 00:00:00";
            $to = date("Y-m-d", $this->to_date). " 23:59:59";
            \Log::debug("From Date: " . $from);
            \Log::debug("To Date: " . $to);

            return $query->whereBetween('fel_invoice_requests.fechaEmision', [$from, $to]);
        } else {
            return $query;
        }

    }


    public function generateReport()
    {

        $query_items = \DB::table('invoices')->join('fel_invoice_requests', 'invoices.id', '=', 'fel_invoice_requests.id_origin')->where('fel_invoice_requests.company_id', $this->company_id)->where('fel_invoice_requests.typeDocument', Documents::NOTA_RECEPCION)->whereNull('fel_invoice_requests.deleted_at');

        if ($this->user && ! $this->user->hasPermission('view_invoice')) {

            \Log::debug("Filter By User: " . $this->user->id);
            // Join with Invoices

            $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
        }

        $query_items = $this->addDateFilter($query_items);

        $query_items = $this->addBranchFilter($query_items);

        $detalles = $query_items->pluck('fel_invoice_requests.detalles', 'fel_invoice_requests.id');

        $invoices = $query_items->select('fel_invoice_requests.id', 'fel_invoice_requests.codigoSucursal', 'fel_invoice_requests.nombreRazonSocial', 'fel_invoice_requests.numeroDocumento', 'fel_invoice_requests.montoTotal', 'fel_invoice_requests.extras')->get();

        $invoices_grouped = collect($invoices)->groupBy('id');

        $items = collect( $detalles )->map( function( $detail, $key ) use ($invoices_grouped) {

            $invoice_data = json_decode(json_encode($invoices_grouped[$key]), true) ;

            $extras = json_decode($invoice_data[0]['extras']);
            $invoice_data[0]['turno'] = isset($extras) ? $extras->turno : '';
            
            $detail = json_decode($detail, true);
            
            $joined = collect($invoice_data)->crossJoin($detail)->all();

            $detalle = collect($joined)->map( function ( $d ) {

                $merged = array_merge(...collect($d)->toArray());

                return $merged;

            })->all();

            return $detalle;

        })->values();

        $items = ExportUtils::flatten_array($items);

        $items_grouped = collect($items)->groupBy('codigoProducto')->all();

        $items_resume = collect( $items_grouped )->map( function( $product_group ) {

            $group_by_turn = collect($product_group)->groupBy('turno')->all();

            $totals_tarde = 0;
            $totals_manana = 0;

            if(isset($group_by_turn['Tarde'])) {

                $totals_tarde = collect($group_by_turn['Tarde'])->sum('subTotal');

            }
            if(isset($group_by_turn['Mañana'])) {

                $totals_manana = collect($group_by_turn['Mañana'])->sum('subTotal');

            }

            $total_return = [
                'descripcion' => explode('-', $product_group[0]['descripcion'])[0],
                'tarde' => $totals_tarde,
                'manana' => $totals_manana,
                'total' =>  $totals_manana + $totals_tarde,
            ];

            return $total_return;

        })->values();

        return [
            "header" => [
                "sucursal" => $this->branch_desc,
                "usuario" => $this->user->name(),
                "fechaReporte" => date('Y-m-d', $this->from_date)
            ],
            "totales" =>[
                "montoTotalGeneral" => NumberUtils::number_format_custom(collect($items)->sum('subTotal'), 2)
            ],
            "items" => $items_grouped,
            "resume" => $items_resume
        ];
        
    }

}
