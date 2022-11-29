<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\BitacoraLog;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Repository\FelCredentialRepository;
use Illuminate\Http\Request;
use EmizorIpx\ClientFel\Services\Connection\Connection;
use EmizorIpx\ClientFel\Utils\ExportUtils;
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
        $from = "2022-03-21 00:00:00";
        $to = "2022-03-21 23:59:59";
        
        $emitted = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->leftJoin('paymentables', 'paymentables.paymentable_id', 'invoices.id')
        ->leftJoin('payments', 'payments.id', 'paymentables.payment_id')
        ->leftJoin('payment_types', 'payments.type_id', 'payment_types.id')
        ->where('fel_invoice_requests.company_id', $this->company_id)
        ->whereNotNull('fel_invoice_requests.cuf')
        ->whereBetween('fel_invoice_requests.fechaEmision', [$from, $to])
        ->whereNotBetween('paymentables.created_at', [$from, $to])
        ->selectRaw(\DB::raw('fel_invoice_requests.id, fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", if(paymentables.created_at is null,"Por pagar","PAGADO") ) AS estado, fel_invoice_requests.codigoCliente,fel_invoice_requests.numeroDocumento, fel_invoice_requests.nombreRazonSocial, payment_types.name as tipoPago, paymentables.created_at as fechaPago, fel_invoice_requests.detalles, fel_invoice_requests.usuario,fel_invoice_requests.montoTotal, JSON_EXTRACT(extras,"$.poliza") as poliza, JSON_EXTRACT(extras,"$.agencia") as agencia'));

        $emittend_payed = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->leftJoin('paymentables', 'paymentables.paymentable_id', 'invoices.id')
        ->leftJoin('payments', 'payments.id', 'paymentables.payment_id')
        ->leftJoin('payment_types', 'payments.type_id', 'payment_types.id')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf')
            ->whereBetween('fel_invoice_requests.fechaEmision', [$from, $to])
            ->whereBetween('paymentables.created_at', [$from, $to])
            ->selectRaw(\DB::raw('fel_invoice_requests.id, fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", if(paymentables.created_at is null,"Por pagar","PAGADO") ) AS estado, fel_invoice_requests.codigoCliente,fel_invoice_requests.numeroDocumento, fel_invoice_requests.nombreRazonSocial, payment_types.name as tipoPago, paymentables.created_at as fechaPago, fel_invoice_requests.detalles, fel_invoice_requests.usuario,fel_invoice_requests.montoTotal, JSON_EXTRACT(extras,"$.poliza") as poliza, JSON_EXTRACT(extras,"$.agencia") as agencia'))
        ;

        $payed = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->leftJoin('paymentables', 'paymentables.paymentable_id', 'invoices.id')
        ->leftJoin('payments', 'payments.id', 'paymentables.payment_id')
        ->leftJoin('payment_types', 'payments.type_id', 'payment_types.id')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf')
            ->whereNotBetween('fel_invoice_requests.fechaEmision', [$from, $to])
            ->whereBetween('paymentables.created_at', [$from, $to])
            ->selectRaw(\DB::raw('fel_invoice_requests.id, fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", if(paymentables.created_at is null,"Por pagar","PAGADO") ) AS estado, fel_invoice_requests.codigoCliente,fel_invoice_requests.numeroDocumento, fel_invoice_requests.nombreRazonSocial, payment_types.name as tipoPago, paymentables.created_at as fechaPago, fel_invoice_requests.detalles, fel_invoice_requests.usuario,fel_invoice_requests.montoTotal, JSON_EXTRACT(extras,"$.poliza") as poliza, JSON_EXTRACT(extras,"$.agencia") as agencia'))
        ;

        $debts = \DB::table('fel_invoice_requests')
        ->leftJoin('invoices', 'invoices.id', 'fel_invoice_requests.id_origin')
        ->leftJoin('paymentables', 'paymentables.paymentable_id', 'invoices.id')
        ->leftJoin('payments', 'payments.id', 'paymentables.payment_id')
        ->leftJoin('payment_types', 'payments.type_id', 'payment_types.id')
        ->where('fel_invoice_requests.company_id', $this->company_id)
            ->whereNotNull('fel_invoice_requests.cuf')
            ->whereBetween('fel_invoice_requests.fechaEmision', [$from, $to])
            ->whereNull('paymentables.created_at')
            ->selectRaw(\DB::raw('fel_invoice_requests.id, fel_invoice_requests.fechaEmision,fel_invoice_requests.numeroFactura, if(fel_invoice_requests.codigoEstado =691 or fel_invoice_requests.codigoEstado = 905, "ANULADO", if(paymentables.created_at is null,"Por pagar","PAGADO") ) AS estado, fel_invoice_requests.codigoCliente,fel_invoice_requests.numeroDocumento, fel_invoice_requests.nombreRazonSocial, payment_types.name as tipoPago, paymentables.created_at as fechaPago, fel_invoice_requests.detalles, fel_invoice_requests.usuario,fel_invoice_requests.montoTotal, JSON_EXTRACT(extras,"$.poliza") as poliza, JSON_EXTRACT(extras,"$.agencia") as agencia'))
        ->union($emitted)
        ->union($emittend_payed)
        ->union($payed);



        // if ($this->user && !$this->user->hasPermission('view_invoice')) {

        //     \Log::debug("Filter By User: " . $this->user->id);

        //     $emitted = $emitted->where('invoices.user_id', '=', $this->user->id);
        //     $emittend_payed = $emittend_payed->where('invoices.user_id', '=', $this->user->id);
        //     $payed = $payed->where('invoices.user_id', '=', $this->user->id);
        //     $debts = $debts->where('invoices.user_id', '=', $this->user->id);
        // }
        // return $debts->get()->sortBy('numeroFactura');
        
        $query_items = $debts->get();
        // $query_items = collect($query_items)->sortBy('numeroFactura');
        // return $query_items;
     
        

        $detalles = $query_items->pluck('detalles', 'id');


        $invoices_grouped = collect($query_items)->groupBy('id');
        
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
        return $items;


        $logs = BitacoraLog::orderBy("id","desc")->simplePaginate(30);

        return view('clientfel::bitacora', compact('logs') );
        
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
