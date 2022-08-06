<?php

namespace EmizorIpx\ClientFel\Reports\ItemInvoice;

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\ItemInvoiceResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class ItemInvoiceReport extends BaseReport implements ReportInterface {

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
                array_push( $branch_access, $this->branch_desc . ($this->branch_desc == 0 ? " Casa Matriz" : " Sucursal " . $value) );  
            }

            $this->branch_desc = implode(" - ", $branches_desc);

            return $query->whereIn('fel_invoice_requests.codigoSucursal', $branch_access);

        }

        return $query;
    }


    public function generateReport()
    {

        $query_items = FelInvoiceRequest::where('company_id', $this->company_id)->where('estado', 'VALIDO');

        if ($this->user && ! $this->user->hasPermission('view_invoice')) {

            \Log::debug("Filter By User: " . $this->user->id);

            $query_items = $query_items->where('user_id', '=', $this->user->id);
        }

        $query_items = $this->addDateFilter($query_items);

        $query_items = $this->addBranchFilter($query_items);

        $detalles = $query_items->pluck('detalles', 'cuf');

        $invoices = $query_items->select('cuf', 'fechaEmision', 'numeroFactura', 'codigoSucursal', 'nombreRazonSocial', 'numeroDocumento', 'montoTotal')->get();

        $invoices_grouped = collect($invoices)->groupBy('cuf');

        $items = collect( $detalles )->map( function( $detail, $key ) use ($invoices_grouped) {

            $invoice_data = $invoices_grouped[$key];
            
            $joined = collect($invoice_data)->crossJoin($detail)->all();

            $detalle = collect($joined)->map( function ( $d ) {

                $merged = array_merge(...collect($d)->toArray());

                return $merged;

            })->all();

            \Log::debug("Invoice Joined: " . json_encode($detalle));

            return $detalle;

        })->values();

        $items = ExportUtils::flatten_array($items);

        return [
            "header" => [
                "sucursal" => $this->branch_desc,
                "usuario" => $this->user->name(),
                "fechaReporte" => Carbon::now()->toDateTimeString()
            ],
            "totales" =>[
                "montoTotalGeneral" => NumberUtils::number_format_custom(collect($items)->sum('subTotal'), 2)
            ],
            "items" => ItemInvoiceResource::collection($items)->resolve()
        ];
        
    }

}