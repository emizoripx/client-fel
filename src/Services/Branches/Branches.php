<?php

namespace EmizorIpx\ClientFel\Services\Branches;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\BaseConnection;
use Exception;

class Branches extends BaseConnection{


    protected $accessToken;

    protected $response;
    
    protected $host;

    public function __construct($accessToken, $host)
    {
        $this->accessToken = $accessToken;

        parent::__construct($host);
    }


    public function getBranches(){
        try{
            $response = $this->client->request('GET', '/api/v1/sucursales', ["headers" => ["Authorization" => "Bearer " . $this->accessToken]]);

            return $this->parse_response($response);

        } catch(Exception $ex){

            \Log::error($ex->getMessage());

            throw new ClientFelException("Error a listar las Sucursales " . $ex->getMessage());

        }
    }

}