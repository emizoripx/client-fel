<?php
namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Services\Connection\Connection ;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ConnectionController extends BaseController
{

    protected $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }
 
    public function registerCredentials(Request $request)
    {

        $input = $request->only(['client_id', 'client_secret']);

        if ($input['client_id'] && $input['client_id'] == "") {
            $error[] = "client_id is required";
        }

        if ($input['client_secret'] && $input['client_secret'] == "") {
            $error[] = "client_secret is required";
        }
    
        if( !empty($error) ) {

            return response()->json(['success' => false, "msg" => $error]);
        }

        $input['grant_type'] = "client_credentials";
        $input['account_id'] = auth()->user()->company()->id;
        $credentials = FelClientToken::createOrUpdate($input);

        try{

            $data = [
                "grant_type" => "client_credentials",
                "client_id" => $credentials->getClientId(),
                "client_secret" => $credentials->getClientSecret()
            ];

            \Log::debug("data : " . json_encode($data));
            $response = $this->connection->authenticate($data);
    
            $credentials->setTokenType($response['token_type']);
            $credentials->setExpiresIn($response['expires_in']);
            $credentials->setAccessToken($response['access_token']);
            $credentials->save();

            \Log::debug("credentials : " . json_encode($credentials));
            return response()->json([
                "success" =>true,
                "credentials" => $credentials
            ]);

        } catch( ClientFelException $ex) {

            Log::error("Error en : " . json_encode($ex->getMessage()));

            return response()->json([
                "success" =>false,
                "credentials" => []
            ]);
        }
        
      
    }


    public function getToken()
    {
        $companyId = 1;
        // $companyId = auth()->user()->company()->id
       
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