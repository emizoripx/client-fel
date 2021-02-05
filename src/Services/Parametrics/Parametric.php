<?php

namespace EmizorIpx\ClientFel\Services;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use Illuminate\Support\Facades\Log;

class Parametric extends BaseConnection
{

    protected $response;

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
        parent::__construct();
    }

    public function get($type)
    {
        try {

            $response = $this->client->request('GET', '/api/v1/' . $type, ["headers" => ["Authorization" => "Bearer " . $this->accessToken]]);

            $this->setResponse($this->parse_response($response));

            return $this->parse_response($response);
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
