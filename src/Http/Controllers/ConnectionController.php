<?php
namespace EmizorIpx\ClientFel\Http\Controllers;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Repository\FelCredentialRepository;
use EmizorIpx\ClientFel\Services\Connection\Connection ;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ConnectionController extends Controller
{

    protected $credentialrepo;

    protected $connection;

    public function __construct(FelCredentialRepository $credential_repo, Connection $connection)
    {
        $this->credentialrepo = $credential_repo;
        $this->connection = $connection;
    }
 
    public function registerCredentials(Request $request)
    {
        $input = $request->only(['client_id', 'client_secret']);

        try{

            $cred = $this->credentialrepo
                ->setCredentials($input['client_id'],$input['client_secret'])
                ->setCompanyId(auth()->user()->company()->id)
                ->register()
                ->syncParametrics();

            return response()->json([
                "success" =>true,
                "credentials" => $cred->getCredential()
            ]);

        } catch( ClientFelException $ex) {

            Log::error("Error en : " . json_encode($ex->getMessage()));

            return response()->json([
                "success" =>false,
                "credentials" => [],
                "msg" => $ex->getMessage()
            ]);
        }
        
      
    }


    public function getToken()
    {
       
        $felClienttoken = FelClientToken::whereAccountId(request()->company_id)->first();

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