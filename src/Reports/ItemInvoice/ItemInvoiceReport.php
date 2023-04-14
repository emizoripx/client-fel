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

    protected $headers_csv;

    protected $branch_desc = "Todos";

    public function __construct( $company_id, $request, $columns, $user, $headers_csv = null )
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch_code') ? $request->get('branch_code') : null;

        $from = $request->has('from_date') ? $request->get('from_date') : null;
        $to = $request->has('to_date') ? $request->get('to_date') : null;

        $this->columns = $columns;

        $this->headers_csv = $headers_csv;

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


    public function generateReport()
    {

        $query_items = \DB::table('invoices')->join('fel_invoice_requests', 'invoices.id', '=', 'fel_invoice_requests.id_origin')->where('fel_invoice_requests.company_id', $this->company_id)->where('fel_invoice_requests.codigoEstado', 690);

        if ($this->user && ! $this->user->hasPermission('view_invoice')) {

            \Log::debug("Filter By User: " . $this->user->id);
            // Join with Invoices

            $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
        }

        $query_items = $this->addDateFilter($query_items);

        $query_items = $this->addBranchFilter($query_items);

        $detalles = $query_items->pluck('fel_invoice_requests.detalles', 'fel_invoice_requests.cuf');

        $invoices = $query_items->select('fel_invoice_requests.cuf', 'fel_invoice_requests.fechaEmision', 'fel_invoice_requests.numeroFactura', 'fel_invoice_requests.codigoSucursal', 'fel_invoice_requests.nombreRazonSocial', 'fel_invoice_requests.numeroDocumento', 'fel_invoice_requests.montoTotal')->get();

        $invoices_grouped = collect($invoices)->groupBy('cuf');

        $items = collect( $detalles )->map( function( $detail, $key ) use ($invoices_grouped) {

            $invoice_data = json_decode(json_encode($invoices_grouped[$key]), true) ;
            
            $detail = json_decode($detail, true);
            
            $joined = collect($invoice_data)->crossJoin($detail)->all();

            $detalle = collect($joined)->map( function ( $d ) {

                $merged = array_merge(...collect($d)->toArray());

                return $merged;

            })->all();


            return $detalle;

        })->values();

        $items = ExportUtils::flatten_array($items);

        $header = [
                "sucursal" => $this->branch_desc,
                "usuario" => $this->user->name(),
                "fechaReporte" => Carbon::now()->timezone('America/La_Paz')->toDateTimeString()
            ];
        return [
            "header" => (empty($this->headers_csv) || is_null($this->headers_csv)) ? $header : $this->headers_csv ,
            "totales" =>[
                "montoTotalGeneral" => NumberUtils::number_format_custom(collect($items)->sum('subTotal'), 2)
            ],
            "items" => ItemInvoiceResource::collection($items)->resolve()
        ];
        
    }

}
