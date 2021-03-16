<?php
namespace EmizorIpx\ClientFel\Services\Products;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use EmizorIpx\ClientFel\Services\BaseConnection;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

class Products extends BaseConnection
{
    protected $accessToken;

    protected $data;

    protected $response;

    public function __construct($accessToken, $host)
    {
        $this->accessToken = $accessToken;
        parent::__construct($host);
    }

    public function setResponse($response) 
    {
        $this->response = $response;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    private function validateData()
    {
        $input = $this->data;

        if ($input['codigo_producto'] && $input['codigo_producto'] == "") {
            $error[] = "codigo_producto is required";
        }

        if ($input['codigo_producto_sin'] && $input['codigo_producto_sin'] == "") {
            $error[] = "codigo_producto_sin is required";
        }

        if ($input['codigo_unidad'] && $input['codigo_unidad'] == "") {
            $error[] = "codigo_unidad is required";
        }

        if ($input['nombre_unidad'] && $input['nombre_unidad'] == "") {
            $error[] = "nombre_unidad is required";
        }

        if (!empty($error)) {

            throw new ClientFelException("Errores en : " . json_encode($error));
        }

        $input['codigoProducto'] = $input['codigo_producto'];

        $input['codigoProductoSIN'] = $input['codigo_producto_sin'];


        $this->setData($input);
    }

    public function saveResponse()
    {
        $input = $this->response;
        
        $input['nombre_unidad'] = $this->data['nombre_unidad']; 
        $input['codigo_unidad'] = $this->data['codigo_unidad']; 
        
        FelSyncProduct::create($input);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function homologate()
    {
        $this->validateData();

        try {
            
            $response = $this->client->request('POST','/api/v1/productos',['json' => $this->data, "headers" => ["Authorization" => "Bearer " . $this->accessToken] ]);

            $this->setResponse($this->parse_response($response));
            return $this->parse_response($response);

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            throw new ClientFelException("Error en la homologacion del producto: " . $ex->getMessage());
        } 
    }
}