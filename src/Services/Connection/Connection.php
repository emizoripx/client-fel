<?php

namespace EmizorIpx\ClientFel\Services\Connection;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;

use EmizorIpx\ClientFel\Services\BaseConnection;
use Illuminate\Support\Facades\Log;

class Connection extends BaseConnection 
{

    public function __construct()
    {
        parent::__construct();
    }

    
    public function authenticate($data)
    {
        try {
            $response = $this->client->request('POST', '/oauth/token', ['json' => $data]);
            
            
            return $this->parse_response($response);

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());
            
            throw new ClientFelException("Error en la autenticación");

        }
        
    }



}