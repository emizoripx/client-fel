<?php

namespace EmizorIpx\ClientFel\Reports\Invoices;

use App\Models\Product;
use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;

class CosinCantidadReport extends BaseReport implements ReportInterface {

    protected $branch_code;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $branch_desc = "Todos";

    public function __construct($company_id, $request, $columns, $user )
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


    public function generateReport()
    {
        \Log::debug("Generar Reporte: ");
        $query_items = \DB::table('invoices')->join('fel_invoice_requests', 'invoices.id', '=', 'fel_invoice_requests.id_origin')->where('fel_invoice_requests.company_id', $this->company_id)->whereNotNull('cuf');

        if ($this->user && ! $this->user->hasPermission('view_invoice')) {

            \Log::debug("Filter By User: " . $this->user->id);

            $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
        }

        $query_items = $this->addDateFilter($query_items);

        $query_items = $this->addBranchFilter($query_items);

        $detalles = $query_items->pluck('invoices.line_items', 'fel_invoice_requests.cuf');

        $select = \DB::raw('fel_invoice_requests.cuf, fel_invoice_requests.codigoCliente, fel_invoice_requests.numeroFactura, fel_invoice_requests.nombreRazonSocial, round(invoices.balance,2) as pagado, round(fel_invoice_requests.montoTotal,2) as montoTotal, fel_invoice_requests.estado');
        $query_invoices = $query_items->select($select);

        $query_invoices = $query_invoices->selectRaw('round(fel_invoice_requests.montoTotal - invoices.balance, 2)  as deuda');
        $query_invoices = $query_invoices->selectRaw('DATE_FORMAT(fel_invoice_requests.fechaEmision, "%Y-%m-%d %H:%i:%s") as fechaEmision');
        
        \Log::debug("SQL QUERY INVOICES: " . $query_invoices->toSql() );

        $invoices = $query_invoices->get();

        $invoices_grouped = collect($invoices)->groupBy('cuf');


        $products_keys = Product::where('company_id', $this->company_id)->select('id', \DB::raw('0 as default_value'))->get()->pluck('default_value', 'hashed_id'); 
        
        \Log::debug("Factura Agrupado: " , [$products_keys]);

        $items = collect( $detalles )->map( function( $detail, $key ) use ($invoices_grouped, $products_keys) {

            $invoice_data = json_decode(json_encode($invoices_grouped[$key]), true) ;
            
            $detail = json_decode($detail, true);
            
            // $joined = collect($invoice_data)->crossJoin($detail)->all();

            $total_quantity = collect($detail)->sum('quantity');

            $detalle = collect($detail)->pluck('quantity', 'product_id')->all();

            $merged = array_merge($invoice_data[0], collect($products_keys)->toArray(), $detalle, ['totalCantidad' => $total_quantity]);

            return $merged;

        })->values();


        return [
            "items" => collect($items)->toArray()
        ];
    }

}