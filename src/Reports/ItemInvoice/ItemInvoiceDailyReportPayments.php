<?php

namespace EmizorIpx\ClientFel\Reports\ItemInvoice;

use EmizorIpx\ClientFel\Reports\BaseReport;
use EmizorIpx\ClientFel\Reports\ReportInterface;
use EmizorIpx\ClientFel\Utils\ExportUtils;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Hashids\Hashids;
use Carbon\Carbon;
class ItemInvoiceDailyReportPayments extends BaseReport implements ReportInterface
{

    protected $branch_code;

    protected $columns;

    protected $company_id;

    protected $user;

    protected $branch_desc = "Todos";

    protected $all_users;

    protected $user_selected;

    public function __construct($company_id, $request, $columns, $user)
    {
        $this->company_id = $company_id;

        $this->branch_code = $request->has('branch') ? $request->get('branch') : null;

        $this->from = $request->has('from_date') ? $request->get('from_date') : null;
        $this->to = $request->has('to_date') ? $request->get('to_date') : null;

        $this->user_selected = null;

        $this->all_users = $request->has('all_users') ? ($request->get('all_users') == "true" ? true : false) : false;
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $this->user = $user;
        if (!$this->all_users) {

            $user_selected = $request->has('user') ? (!empty($request->get('user')) ? $hashid->decode($request->get('user')) : null) : null;
            $cu = null;
            if (!is_null($user_selected)) {
                $cu = \App\Models\CompanyUser::whereUserId($user_selected)->whereCompanyId($company_id)->first();
                \Log::debug("user selected ID = ", $user_selected);
            }
            if (!empty($cu) && !is_null($cu)) {
                $this->user_selected = $cu;
            }
        }

        $this->columns = $columns;

        parent::__construct($this->from, $this->to);
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
        info("Generando reporte MOVIMIENTO DIARIO COTEOR PAYMENTS");
        $from = date('Y-m-d', $this->from_date) . " 00:00:00";
        $to = date("Y-m-d", $this->to_date) . " 23:59:59";

        $emitted = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf')
            ->whereBetween('fel_invoice_requests.fechaEmision', [$from, $to])
            ->selectRaw(\DB::raw('fel_invoice_requests.codigoMetodoPago, fel_invoice_requests.id, codigoEstado, fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", "PAGADO" ) AS estado, fel_invoice_requests.codigoCliente,fel_invoice_requests.numeroDocumento, fel_invoice_requests.nombreRazonSocial, fel_invoice_requests.detalles, fel_invoice_requests.usuario,fel_invoice_requests.montoTotal,fel_invoice_requests.descuentoAdicional, JSON_EXTRACT(extras,"$.poliza") as poliza, JSON_EXTRACT(extras,"$.agencia") as agencia'));

        $emitted = $this->addBranchFilter($emitted);
        \Log::debug("ALL USERS STATUS VARIABLE : " . $this->all_users);
        if (!$this->all_users) {

            if (!is_null($this->user_selected)) {
                \Log::debug("using user select _id : " . $this->user_selected->user_id);
                $emitted = $emitted->where('invoices.user_id', '=', $this->user_selected->user_id);
            } else if ($this->user && !$this->user->hasPermission('view_invoice')) {

                \Log::debug("Filter By User: " . $this->user->id);

                $emitted = $emitted->where('invoices.user_id', '=', $this->user->id);
            }
        }
        $query_items = $emitted->get();

        $detalles = $query_items->pluck('detalles', 'id');

        $invoices_grouped = collect($query_items)->groupBy('id');
        $dictionary_payment_types = \DB::table('fel_payment_methods')->pluck('descripcion', 'codigo');
        $items = collect($detalles)->map(function ($detail, $key) use ($invoices_grouped, $dictionary_payment_types) {

            $invoice_data = json_decode(json_encode($invoices_grouped[$key]), true);

            $detail = json_decode($detail, true);

            $invoice_data[0]['codigoMetodoPago'] = $dictionary_payment_types[$invoice_data[0]['codigoMetodoPago']];
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
        $codigoMetodoPago = null;

        $q_revocated_invoices = 0;
        $q_revocated_invoices = collect($items)->groupBy('numeroFactura')->filter(function ($it, $k) {
            return in_array($it[0]['codigoEstado'], [691, 905]);
        })->count();

        $items_changed = collect($items)->map(function ($item, $key) use (&$invoice_date, &$invoice_number, &$codigoMetodoPago) {

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
                if ($codigoMetodoPago == $item['codigoMetodoPago']) {
                    $item['codigoMetodoPago'] = "";
                    $item['fechaPago'] = "";
                }
                $codigoMetodoPago = $item['codigoMetodoPago'];
                $item['usuario'] = "";
            } else {

                $invoice_date = $item['fechaEmision'];

                $invoice_number = $item['numeroFactura'];

                $codigoMetodoPago = $item['codigoMetodoPago'];
            }

            return $item;
        });


        $item_summary = FelSyncProduct::leftJoin('products', 'products.id', 'fel_sync_products.id_origin')->where('fel_sync_products.company_id', $this->company_id)->select(\DB::raw('codigo_producto, 0 as amount, notes'))->get();
        $item_summary = collect($item_summary)->groupBy('codigo_producto');

        collect($items_changed)->groupBy('codigoProducto')->each(function ($item, $key) use (&$item_summary) {

            if (isset($item_summary[$key])) {
                foreach ($item_summary[$key] as $i) {
                    $i->amount = collect($item)->sum('subTotal');
                }
            }
        })->values();

        $items_array = [];
        $q_products = sizeof($item_summary);
        $counter = 0;

        foreach ($item_summary as $key => $value) {
            $counter++;
            if ($counter >  $q_products / 2) {
                $items_array[1][$key] = $value;
            } else {
                $items_array[0][$key] = $value;
            }
        }
        $items_array[0] = collect($items_array[0])->flatten(1);
        $items_array[1] = collect($items_array[1])->flatten(1);

        $totales = [];
        $total = 0;
        $not_payed = 0.00;
        collect($items_changed)->groupBy('codigoMetodoPago')->map(function ($item, $key) use (&$totales, &$not_payed, &$total) {
            if ($key != "") {
                $totales[] = ["name" => $key, "monto" => $item->sum('montoTotal')];
                $total += $item->sum('montoTotal');
            }
            $not_payed = (float)$not_payed + (float)$item->where('estado', 'Por cobrar')->sum('montoTotal');
        });
        $totales[] = ["name" => "Por cobrar", "monto" => $not_payed];

        return [
            "items_array" => $items_array,
            "company_name" => \App\Models\Company::find($this->company_id)->settings->name,
            "additional_data" => null,
            "username" => $this->user->name(),
            "date" => date("d/m/Y", $this->from),
            "fecha_reporte" => Carbon::now()->timezone('America/La_Paz')->toDateTimeString(),
            "totals" => $totales,
            "total" => $total,
            "literal" => to_word((float)($total), 2, 1),
            "q_revocated_invoices" => $q_revocated_invoices,
        ];
    }
}
