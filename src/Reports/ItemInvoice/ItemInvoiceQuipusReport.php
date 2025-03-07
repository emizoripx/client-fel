<?php

namespace EmizorIpx\ClientFel\Reports\ItemInvoice;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use EmizorIpx\ClientFel\Models\FelBranch;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\ItemInvoiceQuipusReportResource;

class ItemInvoiceQuipusReport extends BaseReport implements ReportInterface {

    protected $branch_code;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $branch_desc = "Todos";

    protected $same_user;
    
    protected $paid_range_filter;

    protected $branch_names;

    public function __construct( $company_id, $request, $columns, $user )
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch') ? $request->get('branch') : null;

        $this->from = $request->has('from_date') ? $request->get('from_date') : null;
        $this->to = $request->has('to_date') ? $request->get('to_date') : null;
        
        $this->columns = $columns;

        $this->branch_names = FelBranch::whereCompanyId($company_id)->pluck("descripcion","codigo");

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
        ini_set('memory_limit', '512M');

        $query_items = \DB::table('fel_invoice_requests')
        ->where('fel_invoice_requests.company_id', $this->company_id)
        ->whereNotNull('fel_invoice_requests.cuf');

        if ($this->same_user) {
            $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
        } else {
            if ($this->user && !$this->user->hasPermission('view_invoice')) {
                $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
            }
        }

        $query_items = $this->addDateFilter($query_items);

        $invoices = $query_items->selectRaw(\DB::raw(
            '
            fel_invoice_requests.fechaEmision,
            fel_invoice_requests.numeroFactura, 
            fel_invoice_requests.numeroDocumento, 
            fel_invoice_requests.nombreRazonSocial, 
            fel_invoice_requests.codigoSucursal, 
            fel_invoice_requests.cuf, 
            fel_invoice_requests.codigoTipoDocumentoIdentidad, 
            fel_invoice_requests.id,
            fel_invoice_requests.codigoEstado,
            fel_invoice_requests.descuentoAdicional, 
            fel_invoice_requests.codigoCliente,
            fel_invoice_requests.detalles, 
            fel_invoice_requests.usuario,
            fel_invoice_requests.montoTotal
         '
        ))->get();

        $detalles = collect($invoices)->pluck('detalles', 'id');

        $invoices_grouped = collect($invoices)->groupBy('id');

        $items = collect($detalles)->map(function ($detail, $key) use ($invoices_grouped) {

            $invoice_data = json_decode(json_encode($invoices_grouped[$key]), true);

            $detail = json_decode($detail, true);

            $joined = collect($invoice_data)->crossJoin($detail)->all();

            $detalle = collect($joined)->map(function ($d) {

                $merged = array_merge(...collect($d)->toArray());

                return $merged;
            })->all();


            return $detalle;
        })->values();

        $items = ExportUtils::flatten_array($items);

        $invoice_date = null;
        $invoice_number = null;

        $items_changed = collect($items)->map(function ($item, $key) use (&$invoice_date, &$invoice_number, &$tipoPago) {

            if (($invoice_date == $item['fechaEmision']) && ($invoice_number == $item['numeroFactura'])) {

                $item['montoTotal'] = 0;
                $item['estado'] = "";
                $item['numeroFactura'] = "";
                $item['fechaEmision'] = "";
                $item['codigoEstado'] = "";
                $item['descuentoAdicional'] = "";
                $item['usuario'] = "";
            } else {

                $invoice_date = $item['fechaEmision'];
                $invoice_number = $item['numeroFactura'];
            }
            $item["sucursal"] = $this->branch_names[intval($item["codigoSucursal"])];

            return $item;
        });


        return [
            "header" => [
                "nit" => \App\Models\Company::find($this->company_id)->settings->id_number,
                "desde" => date('Y-m-d', $this->from_date) . " 00:00:00",
                "hasta" => date('Y-m-d', $this->to_date) . " 23:59:59",
                "fechaReporte" => Carbon::now()->timezone('America/La_Paz')->format("Y-m-d"),
                "usuario" => $this->user->name(),
                "sucursal" => $this->branch_desc,
            ],
            "totales" => ["total" => collect($items_changed)->sum("montoTotal")],
            "items" => ItemInvoiceQuipusReportResource::collection($items_changed)->resolve()
        ];
        
    }

}
