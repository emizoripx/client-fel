<?php

namespace EmizorIpx\ClientFel\Services;

use GuzzleHttp\Client;

Class FelConnection
{
    protected $credencials = array();

    protected $client;

    protected $success;

    protected $response;

    protected $errors;

    protected $status_code;

    protected $access_token;
    /**
 * Input credentials for connection
     */
    public function __construct($host, $token)
    {         
        try {
            $this->ticket = "TICKET";
            $this->client = new Client(
                array(
                    'base_uri' => $host,
                    'http_errors' => false,
                    "connect_timeout" => 5,
                    "timeout" => 30,
                    'redirect.strict' => true,
                    'headers' => array(
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        "Authorization" => "Bearer " . $token,
                        "emizor-header" => 'true',
                    ),
                )
            );
          
            $this->access_token = $token;
        } catch (\Exception $ex) {
            info("ERROR  >>  CONECTION  " . $ex->getMessage());
        }                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      

    }

    public function parse_response($response)
    {
        $response = json_decode((string) $response->getBody(), true);
        
        if (isset($response['status'])) {

            if ($response['status'] != 'success') {

                if (isset($response['errors'])) {
                    return $response['errors'];
                } 
            }

            return $response['data'];
        } else {

            return $response;
        }
    }


    public function checkConnection()
    {
        try {

            info("checking connection...");
            $request = $this->client->options(
                "/api/v1/login", 
                array(
                    "headers" => array(
                    "Authorization" => "Bearer asdfsd"// . $this->access_token
                    ),
                'request.options' => array(
                    'exceptions' => false,
                )

            ));
            $response = $this->client->send($request);
            
            if ($response->getStatusCode() == 200) {
                info("checking connection... respuesta  " . $response->getBody());
                return true;
            }
            info("checking connection... sin conexión" );
            return false;

        } catch (\Exception $ex) {

            throw new \Exception("Problema desconocido " . $ex->getMessage());
        };
    }
    public function handleRequest($method, $endpoint ,$data = null)
    {
        $log_info = request("tstms_small") . "FEL-REQ ";
      
        info($log_info . "SENDING [$method]" . $endpoint );

        $headers = array();
        $options = array();

        if ($method == "POST")
            $response = $this->client->request("POST",$endpoint,["json" => $data->resolve()]);

        if ($method == "PUT")
            $response = $this->client->request("PUT",$endpoint,["json" => $data->resolve()]);

        if ($method == "GET")
            $response = $this->client->get($endpoint, $headers, $options);

        if ($method == "DELETE")
            $response = $this->client->request("DELETE",$endpoint,["json" => $data]);

        info($log_info . "SENT" );

        $this->handleResponse($response);
        
    }

    public function handleResponse($response)
    {
        $log_info = request("tstms_small") . "FEL-RES ";
        info($log_info . "STATUS " . $response->getStatusCode());
        
        $this->status_code = $response->getStatusCode();
        // errors parameters
        if (in_array($response->getStatusCode(), array(400, 404))) {
            logger()->error($log_info . " ERROR DATA => " . $response->getBody());
            $this->setErrors($response);
        }
        
        if ($response->getStatusCode() == 405) {
            logger()->error($log_info . "ERROR DATA => METODO ENDPOINT INCORRECTO O INEXISTENTE");
            $this->setErrors(['code' => 9999, "description" => "Ocurrió un problema en el servicio utilizado, revisar codigo de sucursal"]);
        }
        // errors access
        if (in_array($response->getStatusCode(), array(419, 401))) {
            logger()->error($log_info . "ERROR DATA => ACCESO DENEGADO ". $response->getBody());
            // TODO: generate new token when expire
            $this->setErrors(array("Acceso denegado, su token no es valido"));
        }
        // success
        if (in_array($response->getStatusCode(), array(201, 200))) {
            info($log_info . "SUCCESS DATA => " . $response->getBody());
            $this->setResponse($response);
        }

        // error server
        if ($response->getStatusCode() >= 500) {
            logger()->error($log_info ."ERROR DATA => ID-REQUEST " . $this->ticket . "   ERROR EN SERVER ");
            $this->setErrors(array("Error en el servicio de facturación"));
        }
            
    }

    public function emit($data, $sectorDocument)
    {
        $log_info = request("tstms_small") . "FEL-EMIT ";
        try {
            info($log_info . "DATA =>" . json_encode($data));
            $this->handleRequest("POST", "/api/v1/sucursales/" . $data['codigoSucursal'] . "/facturas/" . $sectorDocument, $data);

        } catch (\Exception $ex) {
            logger()->error($log_info . "ERROR >>> " . $ex->getMessage());
            $this->setErrors(array("Problema desconocido"));
        };
    }

    public function remit($data, $sectorDocument, $factura_ticket)
    {
        $log_info = request("tstms_small") . "FEL-REEMIT ";
        try {
            info($log_info . "DATA =>" . json_encode($data));
            $this->handleRequest("PUT", "/api/v1/sucursales/" . $data['codigoSucursal'] . "/facturas/" . $sectorDocument. "/update/$factura_ticket", $data);

        } catch (\Exception $ex) {
            logger()->error($log_info . "ERROR >>> " . $ex->getMessage());
            $this->setErrors(array("Problema desconocido"));
        };
    }

    public function revocate($ticket, $revocationCode)
    {
        $log_info = request("tstms_small") . "FEL-REVOCATE ";
        try {
            info($log_info . " codigoMotivoAnulacion => $revocationCode");
            $this->handleRequest("DELETE", "/api/v1/facturas/$ticket/anular?unique_code",array("codigoMotivoAnulacion" => $revocationCode));
        }catch (\Exception $ex) {
            logger()->error($log_info . "ERROR >>> " . $ex->getMessage());
            $this->setErrors(array("Problema desconocido"));
        }
    }

    public function getDetails($ticket)
    {
        $log_info = request("tstms_small") . "FEL-DETAILS ";
        try {
            info($log_info . " GETTING... ");
            $this->handleRequest("GET", "/api/v1/facturas/$ticket?unique_code");
        }catch (\Exception $ex) {
            logger()->error($log_info . "ERROR >>> " . $ex->getMessage());
            $this->setErrors(array("Problema desconocido"));
        }
    }

    public function getStatus($ticket)
    {
        $log_info = request("tstms_small") . "FEL-GET-STATUS ";
        try {
            info($log_info . " GETTING... ");
            $this->handleRequest("GET", "/api/v1/facturas/$ticket/status?unique_code");
        }catch (\Exception $ex) {
            logger()->error($log_info . "ERROR >>> " . $ex->getMessage());
            $this->setErrors(array("Problema desconocido"));
        }
    }

    public function checkNit($nit)
    {
        $log_info = request("tstms_small") . "FEL-CHECK-NIT ";
        try {
            info($log_info . " GETTING... ");
            $this->handleRequest("GET", "/api/v1/sucursales/0/validate-nit/$nit");
        } catch (\Exception $ex) {
            logger()->error($log_info . "ERROR >>> " . $ex->getMessage());
            $this->setErrors(array("Problema desconocido"));
        }
    }

    private function setErrors($errors)
    {
        $this->success = false;
        if ( is_array($errors)) {
            $this->errors = $errors;
        }else {
            $this->errors = $this->parse_response($errors); // it detects if status is error, then parse only errors as array
        }
    }

    private function setResponse($response)
    {
        $this->success = true;
        $this->response = $this->parse_response($response);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function isSuccessful()
    {
        return $this->success;
    }
    public function getErrors()
    {
        return $this->errors;
    }

    public function getStatusCodeResponse()
    {
        return $this->status_code;
    }
}