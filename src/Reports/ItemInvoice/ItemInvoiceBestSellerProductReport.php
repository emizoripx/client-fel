<?php

namespace EmizorIpx\ClientFel\Reports\ItemInvoice;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\ItemInvoiceBestSellerProductsResource;

class ItemInvoiceBestSellerProductReport extends BaseReport implements ReportInterface {

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
        \DB::statement(\DB::raw("set @counter := 0"));
        $query_items = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf');

        if ($this->same_user) {
            $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
        } else {
            if ($this->user && !$this->user->hasPermission('view_invoice')) {
                $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
            }
        }

        $query_items = $this->addBranchFilter($query_items);
        $query_items = $this->addDateFilter($query_items);


        $invoices = $query_items->selectRaw(\DB::raw(
            '
            (@counter := @counter +1) as counter,
            fel_invoice_requests.fechaEmision,
            fel_invoice_requests.numeroFactura, 
            fel_invoice_requests.numeroDocumento, 
            fel_invoice_requests.nombreRazonSocial, 
            if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", if(invoices.status_id =3 || invoices.status_id=4  ,"Por cobrar","PAGADO") ) AS estado_pago,
            fel_invoice_requests.estado, 
            fel_invoice_requests.id,
            fel_invoice_requests.codigoEstado,
            fel_invoice_requests.descuentoAdicional, 
            fel_invoice_requests.codigoCliente,
            fel_invoice_requests.detalles, 
            fel_invoice_requests.usuario,
            fel_invoice_requests.montoTotal
         '
        ))->get();

        //ensure only valid, include offline invoices
        $invoices = $invoices->filter(function ($item) {
            return !is_null($item->estado) && (is_null($item->codigoEstado) ||  ($item->codigoEstado != 902 && $item->codigoEstado != 691));
        })->values();

        $agrupado = $invoices->flatMap(function ($factura) {
            return collect(json_decode($factura->detalles))->map(function ($linea) use ($factura) {
                return [
                    'codigo_producto' => $linea->codigoProducto,
                    'nombre_producto' => $linea->descripcion,
                    'cantidad_vendida' => $linea->cantidad,
                    'costo_vendido' => $linea->precioUnitario,
                    'subtotal' => $linea->subTotal,
                ];
            });
        })->groupBy('codigo_producto')->map(function ($items) {
            return [
                'codigo_producto' => $items[0]['codigo_producto'],
                'nombre_producto' => $items[0]['nombre_producto'],
                'cantidad_vendida' => $items->sum('cantidad_vendida'),
                'costo_vendido' => $items[0]['costo_vendido'],
                'subtotal' => $items->sum('subtotal'),
            ];
        })->values();

        $number_top_productos = 10;
        $counter = 0;
        // Obtén los 10 productos más vendidos y agrega un contador.
        $top10 = $agrupado
            ->sortByDesc('cantidad_vendida')
            ->take($number_top_productos)
            ->map(function ($item, $index) use (&$counter) {
                $counter++;
                $item['contador'] = $counter;
                return $item;
            });

        // Obtén los demás productos y calcula la suma de sus cantidades.
        $otros = $agrupado
            ->sortByDesc('cantidad_vendida')
            ->slice($number_top_productos)
            ->values();
        $totalOtros = $otros->sum('cantidad_vendida');

        // Agrega un elemento "OTROS" a la colección con la suma de cantidades.
        $otros = collect([
            [
                'codigo_producto' => 'MENOS DE ' . $number_top_productos . " VENDIDOS",
                'nombre_producto' => 'OTROS',
                'cantidad_vendida' => $totalOtros,
                'costo_vendido' => 0,
                'subtotal' => $otros->sum('subtotal'),
                'contador' => $counter + 1,
            ]
        ]);

        // Combina los 10 productos más vendidos con "OTROS".
        $agrupado = $top10->concat($otros)->values();

        return  [
            "header" => [
                "nit" => \App\Models\Company::find($this->company_id)->settings->id_number,
                "desde" => date('Y-m-d', $this->from_date) . " 00:00:00",
                "hasta" => date('Y-m-d', $this->to_date) . " 23:59:59",
                "fechaReporte" => Carbon::now()->timezone('America/La_Paz')->format("Y-m-d"),
                "usuario" => $this->user->name(),
                "sucursal" => $this->branch_desc,
            ],
            "totales" => ["total" => collect($agrupado)->sum("subtotal")],
            "items" => ItemInvoiceBestSellerProductsResource::collection($agrupado)->resolve()
        ];

        
    }

}
