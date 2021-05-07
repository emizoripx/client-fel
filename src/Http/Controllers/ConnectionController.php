<?php
namespace EmizorIpx\ClientFel\Http\Controllers;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Repository\FelCredentialRepository;
use EmizorIpx\ClientFel\Services\Connection\Connection ;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ConnectionController extends Controller
{

    protected $credentialrepo;

    protected $connection;

    public function __construct(FelCredentialRepository $credential_repo)
    {
        $this->credentialrepo = $credential_repo;
    }
 
    public function registerCredentials(Request $request)
    {
        $input = $request->only(['client_id', 'client_secret']);

        try{

            $cred = $this->credentialrepo
                ->setCredentials($input['client_id'],$input['client_secret'])
                ->setCompanyId(auth()->user()->company()->id)
                ->register()
                ->syncParametrics()
                ->getBranches();

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
        $companyId = auth()->user()->company()->id;
        $felClienttoken = FelClientToken::whereAccountId($companyId)->first();

        $token = $felClienttoken->getAccessToken();
        if ( !empty($token)) {
            return [
                'client_id' => $felClienttoken->getClientId(),
                'client_secret' => $felClienttoken->getClientSecret(),
            ];
        }
        
        if ($felClienttoken) {
            $clientId = $felClienttoken->getClientId();
            $clientSecret = $felClienttoken->getClientSecret();
        } else {
            //TODO: thrown an exception that does not is registed client id or client secret
            return [
                'client_id' => null,
                'client_secret' => null
            ];
        }
        
        $data = [
            "grant_type" => "client_credentials",
            "client_id" => $clientId,
            "client_secret" => $clientSecret
        ];

        try {
            $this->connection = new Connection($felClienttoken->getHost());
            $response = $this->connection->authenticate($data);

            $felClienttoken->setTokenType($response['token_type']);
            $felClienttoken->setExpiresIn($response['expires_in']);
            $felClienttoken->setAccessToken($response['access_token']);
            $felClienttoken->save();

            return [
                'client_id' => $felClienttoken->getClientId(),
                'client_secret' => $felClienttoken->getClientSecret()
            ];

        } catch (\Exception $e) {
            \Log::error($e);
            throw $e;
        }
    }

    public function updateSettings(Request $request){
        $settingsData = $request->get('setting');
        Log::debug($settingsData);

        try {
            $felCompany = AccountPrepagoBags::where('company_id', $request->company_id)->first();
        
            if(!$felCompany){
                return response()->json([
                    "success" =>false,
                    "msg" => "credential not found"
                ]);
            }

            $felCompany->settings = json_encode($settingsData);

            $felCompany->save();

            return response()->json([
                "success" =>true
            ]);

        } catch (Exception $ex) {
            return response()->json([
                "success" =>false,
                "msg" => $ex->getMessage()
            ]);
        }
    }
}