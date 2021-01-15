<?php
namespace EmizorIpx\ClientFel\Services\Products;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\BaseConnection;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\Log;

class Products extends BaseConnection
{
    protected $accessToken;

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
        parent::__construct();
    }


    public function homologate($data)
    {
        try {
            
            $response = $this->client->request('POST','/api/v1/productos',['json' => $data, "headers" => ["Authorization" => "Bearer " . $this->accessToken] ]);

            return $this->parse_response($response);

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            throw new ClientFelException("Error en la homologacion del producto: " . $ex->getMessage());
        } 
    }
}