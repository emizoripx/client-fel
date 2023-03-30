<?php

namespace EmizorIpx\ClientFel\Reports\Orders;

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\ItemResumeResource;
use EmizorIpx\ClientFel\Utils\Documents;

class ItemsReport extends BaseReport implements ReportInterface {

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


    public function generateReport () {

        $query_items = \DB::table('invoices')->join('fel_invoice_requests', 'invoices.id', '=', 'fel_invoice_requests.id_origin')->where('fel_invoice_requests.company_id', $this->company_id)
                                ->where('fel_invoice_requests.typeDocument', Documents::NOTA_RECEPCION)
                                ->whereNull('fel_invoice_requests.deleted_at');

        if ($this->user && ! $this->user->hasPermission('view_invoice')) {

            \Log::debug("Filter By User: " . $this->user->id);
            // Join with Invoices

            $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
        }
        
        $query_items = $this->addDateFilter($query_items);

        $query_items = $this->addBranchFilter($query_items);

        // Has Permission
        \Log::debug("sql: " .  $query_items->toSql());

        $items_array = $query_items->pluck('fel_invoice_requests.detalles');

        $items_array_dec = json_decode($items_array);

        \Log::debug("Data Items: " . json_encode($items_array_dec));

        $items_array = collect($items_array_dec)->map( function ( $detail ) {

            return json_decode($detail, true);
        })->all();

        $items = ExportUtils::flatten_array($items_array);

        $items_grouped = collect($items)->groupBy('codigoProducto')->all();

        \Log::debug("Group By Product Code: " . json_encode($items_grouped));


        $data = collect($items_grouped)->map( function ( $item, $key ) {

            \Log::debug("Key: " . $key);

            $cantidad = collect($item)->sum('cantidad');
            $subTotal = collect($item)->sum('subTotal');
            $montoDescuento = collect($item)->sum('montoDescuento');

            \Log::debug("Cantidad Vendido: " . $cantidad);

            $item_m = [
                'cantidad' => $cantidad,
                'codigoProducto' => $key,
                'descripcion' => explode('-',$item[0]['descripcion'])[0],
                'precioUnitario' => $item[0]['precioUnitario'],
                'montoDescuento' => $montoDescuento,
                'subTotal' => $subTotal
            ];

            $item = $item_m;

            return $item;

        })->values();

        \Log::debug("Data to return from rerport: " . json_encode($data));

        return [
            "header" => [
                "sucursal" => $this->branch_desc,
                "usuario" => $this->user->name(),
                "fechaReporte" => date('Y-m-d', $this->from_date)
            ],
            "totales" =>[
                "montoTotalGeneral" => NumberUtils::number_format_custom(collect($data)->sum('subTotal'), 2),
                "montoTotalGeneralLiteral" => to_word( collect($data)->sum('subTotal'), 2, 1),
            ],
            "items" => ItemResumeResource::collection($data)->resolve()
        ];

    }

    
}
