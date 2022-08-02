<?php

namespace EmizorIpx\ClientFel\Reports\Products;

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\ItemResumeResource;

class ProductReport extends BaseReport implements ReportInterface {

    protected $branch_code;

    protected $columns;

    protected $company_id;

    protected $user_name;


    public function __construct( $company_id, $request, $columns, $user_name )
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch_code') ? $request->get('branch_code') : null;

        $from = $request->has('from_date') ? $request->get('from_date') : null;
        $to = $request->has('to_date') ? $request->get('to_date') : null;

        $this->columns = $columns;

        $this->user_name = $user_name;

        parent::__construct($from, $to);
        
    }

    public function addBranchFilter( $query ) {

        if( !is_null($this->branch_code) ) {

            return $query->where('codigoSucursal', $this->branch_code);

        }

        return $query;
    }

    public function generateReport () {

        $query_items = FelInvoiceRequest::where('company_id', $this->company_id)->where('estado', 'VALIDO');

        $query_items = $this->addDateFilter($query_items);

        $query_items = $this->addBranchFilter($query_items);

        $items_array = $query_items->pluck('detalles');

        $items = ExportUtils::flatten_array($items_array);

        $items_grouped = collect($items)->groupBy('codigoProducto')->all();

        $data = collect($items_grouped)->map( function ( $item, $key ) {

            \Log::debug("Key: " . $key);

            $cantidad = collect($item)->sum('cantidad');
            $subTotal = collect($item)->sum('subTotal');
            $montoDescuento = collect($item)->sum('montoDescuento');

            \Log::debug("Cantidad Vendido: " . $cantidad);

            $item_m = [
                'cantidad' => $cantidad,
                'codigoProducto' => $key,
                'descripcion' => $item[0]['descripcion'],
                'precioUnitario' => $item[0]['precioUnitario'],
                'montoDescuento' => $montoDescuento,
                'subTotal' => $subTotal
            ];

            $item = $item_m;

            return $item;

        })->values();

        return [
            "header" => [
                "sucursal" => is_null($this->branch_code) ?  "Todos" : ($this->branch_code == 0 ? "Casa Matriz" : 'Sucursal ' . $this->branch_code),
                "usuario" => $this->user_name,
                "fechaReporte" => Carbon::now()->toDateTimeString()
            ],
            "totales" =>[
                "montoTotalGeneral" => NumberUtils::number_format_custom(collect($data)->sum('subTotal'), 2)
            ],
            "items" => ItemResumeResource::collection($data)->resolve()
        ];

    }
    
}