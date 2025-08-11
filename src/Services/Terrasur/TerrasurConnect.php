<?php

namespace Emizoripx\ClientFel\Services\Terrasur;

use GuzzleHttp\Client;

class TerrasurConnect {

    protected $client;

    protected $success;

    protected $response;

    protected $errors;

    protected $status_code;

    public function __construct()
    {
        
        try {
        
            $this->client = new Client(
                array(
                    'base_uri' => env("TERRASUR_BASE_URL", NULL),
                    'http_errors' => false,
                    "connect_timeout" => 8,
                    "timeout" => 30,
                    'redirect.strict' => true,
                    'headers' => array(
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        "codigoandroid" => env("TERRASUR_CODE_SERVICE", NULL)
                    ),
                )
            );

        } catch (\Exception $ex) {
            logger()->error("  ERROR " . $ex->getMessage());
        }
    }

    public function handleRequest($method, $endpoint, $action, $data = null)
    {
        
        if ($method == "POST"){
            $data["tipo_accion"] = $action;
            info("SENDING " .json_encode($data). " accion : " . $action. " endpoint => " . $endpoint);
            $response = $this->client->request("POST", $endpoint, ["json" => $data]);
        }

        $this->handleResponse($response);
    }

    public function handleResponse($response)
    {
        $log_info = "TERRASUR ";
        $this->status_code = $response->getStatusCode();
        // errors parameters
        if (in_array($response->getStatusCode(), array(400, 404))) {
            logger()->error($log_info . " ERROR DATA => " . $response->getBody());
            $this->setErrors($response);
        }

        // errors access
        if (in_array($response->getStatusCode(), array(419, 401))) {
            logger()->error($log_info . "ERROR DATA => ACCESO DENEGADO " . $response->getBody());
            $this->setErrors(array("Acceso denegado, su token no es valido"));
        }
        // success
        if (in_array($response->getStatusCode(), array(201, 200))) {
            info($log_info . "SUCCESS DATA => " . $response->getBody());
            $this->setResponse($response);
        }

        // error server
        if ($response->getStatusCode() >= 500) {
            logger()->error($log_info . "ERROR DATA => ID-REQUEST ERROR EN SERVER ");
            $this->setErrors(array("Error en el servicio de facturación"));
        }
    }

    public function getPaymentsQuota($data)
    {
        try {
            $this->handleRequest("POST", "", "solicitar_pago", $data );

        } catch (\Exception $ex) {
            $this->setErrors(array($ex->getMessage()));
        };
         
    }
    public function getPaymentsServices($data)
    {
        try {
            $this->handleRequest("POST", "", "solicitar_pago_servicio", $data );

        } catch (\Exception $ex) {
            $this->setErrors(array($ex->getMessage()));
        };
         
    }
    public function getPaymentTypeQuote($data)
    {
        try {
            $this->handleRequest("POST", "", "solicitar_tipo_pago", $data );

        } catch (\Exception $ex) {
            $this->setErrors(array($ex->getMessage()));
        };
         
    }
    public function getPaymentTypeService($data)
    {
        try {
            $this->handleRequest("POST", "", "solicitar_servicios", $data );

        } catch (\Exception $ex) {
            $this->setErrors(array($ex->getMessage()));
        };
         
    }
    
    public function conciliate($data, $is_service=false)
    {
        
        try {
            $this->handleRequest("POST", "", ($is_service?"conciliar_pago_servicio":"conciliar_pago") , $data );

        } catch (\Exception $ex) {
            $this->setErrors(array($ex->getMessage()));
        };
         
    }

    public function searchClient($data)
    {
        
        try {
            $this->handleRequest("POST", "", "buscar_cliente", $data );

        } catch (\Exception $ex) {
            $this->setErrors(array($ex->getMessage()));
        };
         
    }
    public function searchContract($data)
    {
        
        try {
            $this->handleRequest("POST", "", "solicitar_contrato", $data );

        } catch (\Exception $ex) {
            $this->setErrors(array($ex->getMessage()));
        };
         
    }
    
    private function setErrors($errors)
    {
        $this->success = false;
        if (is_array($errors)) {
            $this->errors = $errors;
        } else {
            $this->errors = $this->parse_response($errors); // it detects if status is error, then parse only errors as array
        }
    }

    private function setResponse($response)
    {
        $this->success = true;
        $this->response = $this->parse_response($response);
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

    public function getResponse()
    {
        return $this->response;
    }

}