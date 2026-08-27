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
    protected $tenant_key;

    public function __construct($accessToken, $host, $tenant_key = null)
    {
        $this->accessToken = $accessToken;
        $this->tenant_key = $tenant_key;
        parent::__construct($host, $accessToken, $tenant_key);
    }

    public function get($type, $updated_at = '', $all='')
    {
        \Log::debug('/api/v1/parametricas/' . $type.'?updated_at=' .$updated_at. '&all=' .$all);
        try {
            $headers = [];
            if (!empty($this->accessToken)) {
                $headers['Authorization'] = 'Bearer ' . $this->accessToken;
            }
            if (!empty($this->tenant_key)) {
                $headers['tenant-key'] = $this->tenant_key;
            }

            if($type == TypeParametrics::TIPOS_DOCUMENTO_SECTOR){
                $response = $this->client->request('GET', '/api/v1/company/' . $type . '?all=' .$all, ['headers' => $headers]);
            } 
            else{
                
                $response = $this->client->request('GET', '/api/v1/parametricas/' . $type.'?updated_at=' .$updated_at. '&all=' .$all, ['headers' => $headers]);
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
