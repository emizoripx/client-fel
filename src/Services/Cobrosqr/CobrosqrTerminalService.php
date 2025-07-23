<?php
namespace EmizorIpx\ClientFel\Services\Cobrosqr;


use EmizorIpx\ClientFel\Models\CompanyUserTerminal;
use App\Models\PaymentHash;
use App\Models\Invoice;
use Carbon\Carbon;

class CobrosqrTerminalService{


    protected $companyUserTerminal;

    protected $url;

    protected $deviceId;

    protected $accessToken;

    protected $expiresAt;

    protected $qrExpiration;

    protected $email;

    protected $password;

    protected $companyGateway;

    public function setCompanyGateway($companyGateway)
    {
        $this->companyGateway = $companyGateway;
        $this->setCredentials();
    }

    public function setCredentials()
    {
        $credentials = $this->companyGateway->getConfig();
        info("TRACKING credentials>>> " , [$credentials]);
        $this->setUrl($credentials->endpoint);
        $this->setAccessToken($credentials->access_token);
        $this->setExpiresAt($credentials->expires_at);
        $this->setDeviceId($credentials->device_id);
        $this->setEmail($credentials->email);
        $this->setPassword($credentials->password);
        $this->setQrExpiration(empty($credentials->qr_expiration)?now()->addDays(1)->setTimezone(config("app.timezone"))->format("Y-m-d H:i:s"):$credentials->qr_expiration);
    }

    public function saveToken($token)
    {
        $data = $this->companyGateway->getConfig();
        
        $data->access_token = $token;
        $data->expires_at = now()->addDays(360)->setTimezone(config("app.timezone"))->format("Y-m-d H:i:s");
        $this->companyGateway->setConfig($data);
        $this->companyGateway->save();
        
        $this->setAccessToken($data->access_token);
        $this->setExpiresAt($data->expires_at);
        info("crendentials saved! " , [$data]);
    }

    public function setQrExpiration($qrExpiration)
    {
       $this->qrExpiration = $qrExpiration;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getQrExpiration()
    {
        return $this->qrExpiration;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }
    public function setDeviceID($deviceId)
    {
        $this->deviceId = $deviceId;
    }

    public function setCompanyUserTerminal()
    {
        $companyUserTerminal = CompanyUserTerminal::where("company_user_id", auth()->user()->token()->cu->id)->first();

        if (empty($companyUserTerminal))
            throw new \Exception("Terminal no configurada");

        $this->companyUserTerminal = $companyUserTerminal;
    }

    public function buildUrl($uri)
    {
        return rtrim($this->url, '/') ."/". $uri;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    public function getAccessToken() 
    {
        if (empty($this->accessToken))
            $this->authenticate();

        if ( Carbon::parse($this->expiresAt)->lt( now()->setTimezone(config('app.timezone')) ) ) {
           
            throw new \Exception("El token a expirado.");
        }

        return $this->accessToken;
    }

    public function getEmail()
    {
        if (empty($this->email))
            throw new \Exception("El email no esta configurado correctamente");

        return $this->email;
    }
    public function getPassword()
    {
        if (empty($this->password))
            throw new \Exception("La contraseña no esta configurada correctamente");

        return $this->password;
    }

    public function authenticate()
    {
        info("TRACKING >>> authenticate" );
        $dataToSend = [
            "email" => $this->getEmail(),
            "password" => $this->getPassword(),
            "token_name" => "EMIZOR-TOKEN",
        ];

        $client = new \GuzzleHttp\Client();

        $response = $client->post($this->buildUrl("api/v1/login"), ['json' => $dataToSend]);

        $responseBody = json_decode($response->getBody(), true);
        
        if ( isset($responseBody["access_token"]) ) {
            info("TRACKING >>> authenticate success");
            $this->saveToken($responseBody["access_token"]);
        } else {
            info("TRACKING >>> authenticate failed");
            throw new \Exception("Problemas al obtener Access token");
        }
    }

    public function scan($code)
    {
        $dataToSend = [
            "code" => $code,
        ];

        $client = new \GuzzleHttp\Client();

        $response = $client->post($this->buildUrl("api/v1/devices/scan/"), ['json' => $dataToSend]);

        $responseBody = json_decode($response->getBody(), true);

        if ($responseBody["success"]) {
            $this->companyUserTerminal->fill( array_merge($responseBody["data"], ["terminal_code" => $code, "company_user_id" => auth()->user()->token()->cu->id]));
            $this->companyUserTerminal->save();
        }else {
            throw new \Exception("Datos incorrectos");
        }
    }

    public function makeRequest($uri, $method = 'GET', $postData = [])
    {
        info("request ====> ". $uri);
        $tries = 3;
        while($tries > 0) {
            info("TRACKING >>> make request");
            $client = new \GuzzleHttp\Client();

            $options = [
                'http_errors' => false,
                "connect_timeout" => 5,
                "timeout" => 30,
                'redirect.strict' => true,
                'headers' => [
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getAccessToken()
                ]
            ];
            info("TRACKING >>> make request options ", [$options]);

            if ($method === 'POST') {
                $options['json'] = $postData;
                $response = $client->post($this->buildUrl($uri), $options);
            } else {
                $response = $client->get($this->buildUrl($uri), $options);
            }
            info("TRACKING >>> make request  ", [$response->getStatusCode()]);
            if (in_array($response->getStatusCode(), [200])) {
                $tries = 0;
            }

            if (in_array($response->getStatusCode(), array(419, 401))) {
                $this->authenticate();
                $tries--;
                continue;
            }

            $responseBody = json_decode($response->getBody(), true);

            info("TRACKING >>> make request ", [$responseBody]);

            if (isset($responseBody['status'])) {
                return $responseBody['data'];
            }
            if (isset($responseBody['success'])) {
                return $responseBody['data'];
            }

            return [];
        }
        
    }

    public function getDeviceId()
    {
        if (empty($this->deviceId))
            throw new \Exception("El identificador del dispositivo no esta configurado correctamente");

        return $this->deviceId;

    }

    public function listPayments()
    {
        return $this->makeRequest("api/v1/devices/payments/" . $this->getDeviceId());
    }

    public function listCashClosures()
    {

        return $this->makeRequest("api/v1/devices/cashclosures/".$this->getDeviceId());
    }

    public function requireQR($total, $only_publish = false, $qr_expiration = null, $description="")
    {

        $data = [
                    "device_id" => $this->getDeviceId(),
                    "amount" => round((float) $total,2),
                    "description" => $description,
                    "modify_amount" => false,
                    "is_multi_use" => false,
                    "qr_expiration" => is_null($qr_expiration)? $this->getQrExpiration():$qr_expiration,  
                ];
        info("data  to send to qr " , $data);
                
        return $this->makeRequest("api/v1/devices/simple-qr/generate" . ($only_publish?"?only_publish":""), "POST", $data);
    }

    public function statusQR($qrId)
    {
        return $this->makeRequest("api/v1/devices/simple-qr/get/$qrId");
    }

    public function callbackPayment($payment_hash, $data)
    {

        $invoice = $payment_hash->fee_invoice;

        try {

            $this->companyGateway
                ->driver($invoice->client)
                ->setPaymentMethod(1000) // gatewattype qr = 1000
                ->setPaymentHash($payment_hash)
                ->processPaymentCallback("Pagado por: ". $data["payment_name"] . " banco: ". $data["payment_bank"]);

        }catch(\Throwable $th) {
            info("error :  callback payment  file:" .$th->getFile() . " message: ". $th->getMessage() . " Line: " .$th->getLine() );
        }
    
    }

}