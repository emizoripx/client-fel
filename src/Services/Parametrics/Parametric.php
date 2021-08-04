<?php

namespace EmizorIpx\ClientFel\Services\Parametrics;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\BaseConnection;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\type;

class Parametric extends BaseConnection
{

    protected $response;

    protected $accessToken;

    public function __construct($accessToken, $host)
    {
        $this->accessToken = $accessToken;
        parent::__construct($host);
    }

    public function get($type, $updated_at = '', $all='')
    {
        \Log::debug('/api/v1/parametricas/' . $type.'?updated_at=' .$updated_at. '&all=' .$all);
        try {

            if($type == TypeParametrics::TIPOS_DOCUMENTO_SECTOR){
                $response = $this->client->request('GET', '/api/v1/company/' . $type, ["headers" => ["Authorization" => "Bearer " . $this->accessToken]]);
            } 
            else{
                
                $response = $this->client->request('GET', '/api/v1/parametricas/' . $type.'?updated_at=' .$updated_at. '&all=' .$all, ["headers" => ["Authorization" => "Bearer " . $this->accessToken]]);
            }

            
             $this->setResponse($this->parse_response($response));

            return $this->response;
        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            throw new ClientFelException("Error al obtener las parametricas: " . $ex->getMessage());
        }
    }

    public function setResponse($value)
    {
        $this->response = $value;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
