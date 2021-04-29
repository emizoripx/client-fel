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

class BitacoraController extends BaseController
{

    public function __construct(FelCredentialRepository $credential_repo)
    {
        $this->credentialrepo = $credential_repo;
    }
    public function index(Request $request)
    {
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
