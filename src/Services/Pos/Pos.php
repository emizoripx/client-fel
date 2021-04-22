<?php

namespace EmizorIpx\ClientFel\Services\Pos;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\BaseConnection;
use Exception;

class Pos extends BaseConnection{

    protected $accessToken;

    protected $response;

    protected $branch_code;

    public function __construct( $accessToken ,$host)
    {
        $this->accessToken = $accessToken;

        parent::__construct($host);
    }


    public function setBranch($branchCode){
        $this->branch_code = $branchCode;
        return $this;
    }

    public function getPOS(){
        try{
            $response = $this->client->request('GET', '/api/v1/puntos-de-venta?branch_code='.$this->branch_code, ["headers" => ["Authorization" => "Bearer " . $this->accessToken]]);

            return $this->parse_response($response);

        } catch(Exception $ex){

            throw new ClientFelException('Error al obtener POS '. $ex->getMessage());
        }
    }

}