<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\BitacoraLog;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Repository\FelCredentialRepository;
use Illuminate\Http\Request;
use EmizorIpx\ClientFel\Services\Connection\Connection;
use Exception;
class BitacoraController extends BaseController
{
    protected $company_id;
    public function __construct(FelCredentialRepository $credential_repo)
    {
        $this->credentialrepo = $credential_repo;
        $this->company_id = 165;
    }
    public function index(Request $request)
    {

        // $from = date('Y-m-d', $this->from_date) . " 00:00:00";
        // $to = date("Y-m-d", $this->to_date) . " 23:59:59";
        $from = "2023-02-13 00:00:00";
        $to = "2023-02-13 23:59:59";

        $emitted = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf')
            ->whereBetween('fel_invoice_requests.fechaEmision', [$from, $to])
            ->whereNotBetween('paymentables.created_at', [$from, $to])
            ->selectRaw(\DB::raw('fel_invoice_request.codigoMetodoPago, fel_invoice_requests.id, codigoEstado, fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", if(paymentables.created_at is null,"Por cobrar","PAGADO") ) AS estado, fel_invoice_requests.codigoCliente,fel_invoice_requests.numeroDocumento, fel_invoice_requests.nombreRazonSocial, payment_types.name as tipoPago, fel_invoice_requests.detalles, fel_invoice_requests.usuario,fel_invoice_requests.montoTotal,fel_invoice_requests.descuentoAdicional, JSON_EXTRACT(extras,"$.poliza") as poliza, JSON_EXTRACT(extras,"$.agencia") as agencia'));

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
        return $query_items;
        $detalles = $query_items->pluck('detalles', 'id');

        $invoices_grouped = collect($query_items)->groupBy('id');
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

        $totales = [];
        $not_payed = 0.00;
        collect($items_changed)->groupBy('codigoMetodoPago')->map(function ($item, $key) use (&$totales, &$not_payed) {
            if ($key != "")
                $totales[] = ["name" => $key, "monto" => $item->sum('montoTotal')];
        });
        $totales[] = ["name" => "Por cobrar", "monto" => $not_payed];

        return [
            "header" => [
                "nit" => \App\Models\Company::find($this->company_id)->settings->id_number,
                "desde" => date('Y-m-d', $this->from) . " 00:00:00",
                "hasta" => date('Y-m-d', $this->to) . " 23:59:59",
                "fechaReporte" => Carbon::now()->format("Y-m-d"),
                "usuario" => $this->user->name(),
                "sucursal" => $this->branch_desc,
                "nombreUsuario" => $this->all_users || is_null($this->user_selected) ? "TODOS" : $this->user_selected->user->name()
            ],
            "totales" => $totales,
            "items" => ItemInvoiceDailyMovementResource::collection($items_changed)->resolve()
        ];

        // $logs = BitacoraLog::orderBy("id","desc")->simplePaginate(30);

        // return view('clientfel::bitacora', compact('logs') );
        
    }

    public function updateTokens()
    {
        $felClienttokens = FelClientToken::where("host",'like',"%sinfel.emizor.com")->get();


        foreach ($felClienttokens as $felClienttoken) {
            $connection = new Connection($felClienttoken->getHost());
        
            $clientId = $felClienttoken->getClientId();
            $clientSecret = $felClienttoken->getClientSecret();
        

            $data = [
                "grant_type" => "client_credentials",
                "client_id" => $clientId,
                "client_secret" => $clientSecret
            ];
            try {

                $response = $connection->authenticate($data);
                
                $felClienttoken->setTokenType($response['token_type']);
                $felClienttoken->setExpiresIn($response['expires_in']);
                $felClienttoken->setAccessToken($response['access_token']);
                $felClienttoken->save();
            } catch (Exception $ex) {
                \Log::debug("NO SE PUEDE AUTENTICAR LA EMPRESA # ". $felClienttoken->account_id ." con client_id : " . $clientId . " client_secret : " . $clientSecret . " con host  " . $felClienttoken->getHost());
            }
        }
        dd("done");

    }
}
