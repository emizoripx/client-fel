<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\BitacoraLog;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Repository\FelCredentialRepository;
use Illuminate\Http\Request;
use EmizorIpx\ClientFel\Services\Connection\Connection;
use Exception;
use App\Models\PaymentHash;
use Hashids\Hashids;
class BitacoraController extends BaseController
{

    public function __construct(FelCredentialRepository $credential_repo)
    {
        $this->credentialrepo = $credential_repo;
    }
    public function index(Request $request)
    {
        $hashids = new Hashids(config('ninja.hash_salt'), 10);
        
        $payments = PaymentHash::get();

        $arr = [];
        foreach ($payments as $p) {
            $data = $p->data;

            if ($data->invoices && isset($data->invoices->invoices) ){
                

                $invoice = $data->invoices->invoices[0];
                $invoice->invoice_id = $hashids->encode($p->data->invoices->invoices[0]->invoice_id);
                $data->invoices = $p->data->invoices->invoices;
                $data->invoices[0] = $invoice;


                $total = $data->total;
                $total->credit_totals = 0;
                $total->invoice_totals = (float)$total->invoice_totals;
                $total->fee_total = (float)$total->fee_total;
                $total->amount_with_fee = (float)$total->amount_with_fee;
                $data->total = $total;
                
                
                $p->data = $data;
                $p->save();
                $arr[] = $data;
            }
            
        }
        return $arr;



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
