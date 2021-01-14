<?php
namespace EmizorIpx\ClientFel\Http\Controllers;

use EmizorIpx\ClientFel\Http\Requests\StoreCredentialsRequest;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Services\Connection\Connection ;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ConnectionController extends Controller
{

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
 
    public function registerCredentials(Request $request)
    {

        $input = $request->only(['client_id', 'client_secret', 'account_id']);

        if ($input['client_id'] && $input['client_id'] == "") {
            $error[] = "client_id is required";
        }

        if ($input['client_secret'] && $input['client_secret'] == "") {
            $error[] = "client_secret is required";
        }
        if ($input['account_id'] && $input['account_id'] == "") {
            $error[] = "account_id is required";
        }

        if( !empty($error) ) {

            return response()->json(['success' => false, "msg" => $error]);
        }

        $input['grant_type'] = "client_credentials";

        $credentials = FelClientToken::create($input);
        
        return response()->json([
            "success" =>true,
            "credentials" => $credentials
        ]);
    }


    public function getToken()
    {
        $companyId = 1;
        if (auth()) {
            if (auth()->user()) {
                if (auth()->user()->company()) {
                    $companyId = $company->id;     
                }
            }
        }
    
        $felClienttoken = FelClientToken::whereAccountId($companyId)->first();

        $token = $felClienttoken->getAccessToken();
        if ( !empty($token)) {
            return [
                'token_type' => $felClienttoken->getTokenType(),
                'expires_in' => $felClienttoken->getExpiresIn(),
                'access_token' => $token
            ];
        }
        
        if ($felClienttoken) {
            $clientId = $felClienttoken->getClientId();
            $clientSecret = $felClienttoken->getClientSecret();
        } else {
            //TODO: thrown an exception that does not is registed client id or client secret
            return [
                'token_type' => null,
                'expires_in' => null,
                'access_token' => null
            ];
        }
        
        $data = [
            "grant_type" => "client_credentials",
            "client_id" => $clientId,
            "client_secret" => $clientSecret
        ];

        try {

            $response = $this->connection->authenticate($data);

            $felClienttoken->setTokenType($response['token_type']);
            $felClienttoken->setExpiresIn($response['expires_in']);
            $felClienttoken->setAccessToken($response['access_token']);
            $felClienttoken->save();

            return [
                'token_type' => $response['token_type'],
                'expires_in' => $response['expires_in'],
                'access_token' => $response['access_token']
            ];

        } catch (\Exception $e) {
            \Log::error($e);
            throw $e;
        }
    }
}