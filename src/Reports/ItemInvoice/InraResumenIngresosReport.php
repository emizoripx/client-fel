<?php

namespace EmizorIpx\ClientFel\Reports\ItemInvoice;

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Http\Resources\ItemInraResumenResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class InraResumenIngresosReport extends BaseReport implements ReportInterface
{

    protected $branch_code;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $branch_desc = "Todos";

    protected $from_date ;
    protected $to_date ;

    public function __construct($company_id, $request, $columns, $user)
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch_code') ? $request->get('branch_code') : null;

        $this->from_date = $request->has('from_date') ? $request->get('from_date') : null;
        $this->to_date = $request->has('to_date') ? $request->get('to_date') : null;

        $this->columns = $columns;

        $this->user = $user;

        parent::__construct($this->from_date, $this->to_date);
    }

    public function addBranchFilter($query)
    {

        if (!is_null($this->branch_code)) {

            \Log::debug("Filter by Brach: " . $this->branch_code);

            $this->branch_desc = "Sucursal " . $this->branch_code;

            return $query->where('fel_invoice_requests.codigoSucursal', $this->branch_code);
        } elseif (count($branch_access = $this->user->getOnlyBranchAccess()) > 0) {

            $branch_access = $this->user->getOnlyBranchAccess();

            \Log::debug("Filter by Access Branch");

            $branches_desc = [];
            foreach ($branch_access as $value) {
                array_push($branches_desc, ($value == 0 ? " Casa Matriz" : " Sucursal " . $value));
            }

            $this->branch_desc = implode(" - ", $branches_desc);

            return $query->whereIn('fel_invoice_requests.codigoSucursal', $branch_access);
        }

        return $query;
    }


    public function generateReport()
    {

        $query_items = \DB::table('invoices')->join('fel_invoice_requests', 'invoices.id', '=', 'fel_invoice_requests.id_origin')->where('fel_invoice_requests.company_id', $this->company_id)->where('fel_invoice_requests.codigoEstado', 690);

        if ($this->user && !$this->user->hasPermission('view_invoice')) {

            \Log::debug("Filter By User: " . $this->user->id);
            // Join with Invoices

            $query_items = $query_items->where('invoices.user_id', '=', $this->user->id);
        }

        $query_items = $this->addDateFilter($query_items);

        $query_items = $this->addBranchFilter($query_items);

        $detalles = $query_items->pluck('invoices.line_items', 'fel_invoice_requests.cuf');

        $invoices = $query_items->select('fel_invoice_requests.cuf', 'fel_invoice_requests.fechaEmision', 'fel_invoice_requests.numeroFactura', 'fel_invoice_requests.codigoSucursal', 'fel_invoice_requests.nombreRazonSocial', 'fel_invoice_requests.numeroDocumento','fel_invoice_requests.montoTotal', 'fel_invoice_requests.descuentoAdicional')->get();

        $invoices_grouped = collect($invoices)->groupBy('cuf');

        $items = collect($detalles)->map(function ($detail, $key) use ($invoices_grouped) {

            $invoice_data = json_decode(json_encode($invoices_grouped[$key]), true);

            $detail = json_decode($detail, true);
            $detail = collect($detail)->map(function ($d) {
                return [
                    "product_key" => $d["product_key"],
                    "codigo_producto" => $d["codigo_producto"],
                    'notes' => $d['notes'],
                    "cost" => $d["cost"],
                    "quantity" => $d["quantity"],
                    "custom_value1" => $d["custom_value1"],
                    "custom_value2" => $d["custom_value2"],
                    "discount" => $d["discount"],
                    "product_id" => $d["product_id"],
                    "line_total" => $d["line_total"],
                ];
            })->all();


            $joined = collect($invoice_data)->crossJoin($detail)->all();

            $detalle = collect($joined)->map(function ($d) {

                $merged = array_merge(...collect($d)->toArray());

                return $merged;
            })->all();


            return $detalle;
        })->values();

        $items = ExportUtils::flatten_array($items);

        $items =  collect($items)->map(function ($item) {
            $item['descuento_prop'] = $item['descuentoAdicional'] / ($item['montoTotal'] + $item['descuentoAdicional']) * $item['line_total'];
            $item['subtotal_prop'] = $item['line_total'] - $item['descuento_prop'];
            return $item;
        });

        $area1 = collect($items)->where('custom_value1', '=' , 'SANNEAMIENTO');
        $area2 = collect($items)->where('custom_value1', '=' , 'CATASTRO');
        $area3 = collect($items)->where('custom_value1', '=' , 'ADMINISTRATIVO');
        $area4 = collect($items)->where('custom_value1', '=' , 'JURIDICA');
        
        return [
            "header" => [
                "sucursal" => $this->branch_desc,
                "usuario" => $this->user->name(),
                "fechaReporte" => Carbon::now()->timezone('America/La_Paz')->toDateTimeString(),
                "from" => date('Y-m-d', $this->from_date) . " 00:00:00",
                "to" => date('Y-m-d', $this->to_date) . " 23:59:59",
            ],
            "totales" => [
                "montoTotalGeneral" => NumberUtils::number_format_custom(collect($items)->sum('line_total'), 2)
            ],
            "area1" => ItemInraResumenResource::collection($area1)->resolve(),
            "area2" => ItemInraResumenResource::collection($area2)->resolve(),
            "area3" => ItemInraResumenResource::collection($area3)->resolve(),
            "area4" => ItemInraResumenResource::collection($area4)->resolve(),
        ];
    }
}
