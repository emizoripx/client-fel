<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\BitacoraLog;
use EmizorIpx\ClientFel\Models\FelClientToken;
use Illuminate\Http\Request;
use EmizorIpx\ClientFel\Services\Connection\Connection;

class BitacoraController extends BaseController
{

    public function index(Request $request)
    {
        $logs = BitacoraLog::orderBy("id","desc")->simplePaginate(30);

        return view('clientfel::bitacora', compact('logs') );
        
    }

    public function updateTokens()
    {
        $felClienttokens = FelClientToken::whereHost("http://sinfel.emizor.com")->get();


        foreach ($felClienttokens as $felClienttoken) {
            # code...
            $connection = new Connection($felClienttoken->getHost());
            $response = $connection->authenticate($data);
            
            $felClienttoken->setTokenType($response['token_type']);
            $felClienttoken->setExpiresIn($response['expires_in']);
            $felClienttoken->setAccessToken($response['access_token']);
            $felClienttoken->save();
        }
        dd("done");

    }
}
