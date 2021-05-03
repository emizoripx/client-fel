<?php

namespace EmizorIpx\ClientFel\Services\Company;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\BaseConnection;
use Exception;

class Company extends BaseConnection{


    protected $accessToken;

    protected $response;
    
    protected $host;

    public function __construct($accessToken, $host)
    {
        $this->accessToken = $accessToken;

        parent::__construct($host);
    }


    public function getCompany(){
        try{
            $response = $this->client->request('GET', '/api/v1/company', ["headers" => ["Authorization" => "Bearer " . $this->accessToken]]);

            return $this->parse_response($response);

        } catch(Exception $ex){

            \Log::error($ex->getMessage());

            throw new ClientFelException("Error al obtener el detalle de Compañia " . $ex->getMessage());

        }
    }

}