<?php

namespace EmizorIpx\ClientFel\Services\Invoices;

use Carbon\Carbon;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\BaseConnection;
use Exception;
use Facade\FlareClient\Http\Client;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;
use stdClass;

class Invoices extends BaseConnection
{
    protected $access_token;

    protected $data;

    protected $type_document;

    protected $branch_number;

    protected $response;

    public function __construct()
    {
        parent::__construct();
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    public function setData($data)
    {
        $this->data  = $data;
    }

    public function setTypeDocument($type_document)
    {
        $this->type_document = $type_document;
    }

    public function setBranchNumber($branch_number)
    {
        $this->branch_number = $branch_number;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function sendToFel()
    {

        $this->checkParameters();

        $this->validateData();

        try {

            $response = $this->client->request('POST', "/api/v1/sucursales/$this->branch_number/facturas/$this->type_document", ["json" => $this->data, "headers" => ["Authorization" => "Bearer " . $this->access_token]]);
            $this->setResponse($this->parse_response($response));
            return $this->parse_response($response);
        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            throw new ClientFelException("Error en la creación de la factura: " . $ex->getMessage());
        }
    }

    public function checkParameters()
    {
        if (empty($this->access_token)) {
            throw new ClientFelException("El access token es necesario");
        }

        if (empty($this->type_document)) {
            throw new ClientFelException("El type_document es necesario");
        }

        if ($this->branch_number < 0) {
            throw new ClientFelException("El branch_number es necesario");
        }

        if (empty($this->data)) {
            throw new ClientFelException("Los datos son necesarios para enviar.");
        }
    }

    public function validateData()
    {

        $rules = [
            "numeroFactura"=> 'required|integer',
            "codigoPuntoVenta"=> 'nullable|integer',
            "fechaEmision"=> "required|date",
            "nombreRazonSocial"=> "required",
            "codigoTipoDocumentoIdentidad"=> "required|integer",
            "numeroDocumento"=> "required",
            "complemento"=> "nullable|string",
            "codigoCliente"=> "required",
            "codigoMetodoPago"=> "required|integer",
            "numeroTarjeta"=> "nullable|integer",
            "montoTotal"=> "required|numeric",
            "codigoMoneda"=> "nullable|integer",
            "montoTotalMoneda"=> "required|numeric",
            "usuario"=> "required|string",
            "emailCliente"=> "nullable|string",
            "telefonoCliente"=> "nullable|string",
            "extras"=> "nullable|string",
            "codigoLeyenda"=> "required|integer",
            "montoTotalSujetoIva"=> "required|numeric",
            "tipoCambio"=> "nullable|numeric",
            "detalles.*.codigoProducto"=> "required|string",
            "detalles.*.descripcion"=> "required|string",
            "detalles.*.unidadMedida"=> "required|integer",
            "detalles.*.precioUnitario"=> "required|numeric",
            "detalles.*.subTotal"=> "required|numeric",
            "detalles.*.cantidad"=> "required|numeric",
            "detalles.*.numeroSerie"=> "nullable|string",
            "detalles.*.montoDescuento"=> "nullable|numeric",
            "detalles.*.numeroImei"=> "nullable|string"
        ];
        $messages = [
            "numeroFactura.required"=> 'El número de factura  es necesario.',
            "numeroFactura.integer"=> 'El número de factura  deber ser entero.',
            "codigoPuntoVenta.integer"=> 'El código de punto de venta debe ser entero',
            "fechaEmision.required"=> "La fecha es requerida",
            "fechaEmision.date"=> "El formato de la fecha no es correcto",
            "nombreRazonSocial.required"=> "La razón social del cliente es necesaria",
            "codigoTipoDocumentoIdentidad.required"=> "El código del tipo de documento es necesario",
            "codigoTipoDocumentoIdentidad.integer"=> "El código del tipo de documento debe ser entero",
            "numeroDocumento.required"=> "El número de documento es necesario",
            "codigoCliente.required"=> "El código del cliente es necesario",
            "codigoMetodoPago.required"=> "El código del método de pago es necesario",
            "codigoMetodoPago.integer"=> "El código del método del pago debe ser entero",
            "montoTotal.required"=> "El monto total es necesario.",
            "montoTotal.numeric"=> "El monto total debe ser numérico",
            "codigoMoneda.integer"=> "El código de moneda deber ser entero",
            "montoTotalMoneda.required"=> "El monto total de la moneda es necesario.",
            "montoTotalMoneda.numeric"=> "El monto total de la moneda debe ser numérico",
            "usuario.required"=> "El nombre del usuario es requerido",
            "emailCliente.email"=> "El email del cliente no es válido",
            "codigoLeyenda.required"=> "El código de leyenda es necesario",
            "codigoLeyenda.integer"=> "El código del leyenda debe ser entero",
            "montoTotalSujetoIva.required"=> "El monto total sujeto a iva es necesario",
            "montoTotalSujetoIva.numeric"=> "El monto total sujeto a iva debe ser numérico",
            "tipoCambio.numeric"=> "El tipo de cambio debe ser numérico",
            "detalles.*.codigoProducto.required"=> "El código del producto es necesario",
            "detalles.*.descripcion.required"=> "La descripción del producto es necesaria",
            "detalles.*.unidadMedida.required"=> "La unidad de medida del producto es necesaria",
            "detalles.*.unidadMedida.integer"=> "La unidad de medida del produto debe ser entero",
            "detalles.*.precioUnitario.required"=> "El precio unitario del producto es necesario",
            "detalles.*.precioUnitario.numeric"=> "El precio unitario del producto debe ser numérico",
            "detalles.*.subTotal.required"=> "El subtotal del producto es necesario",
            "detalles.*.subTotal.numeric"=> "El subtotal del producto debe ser numérico",
            "detalles.*.cantidad.required"=> "La cantidad del producto es necesaria",
            "detalles.*.cantidad.numeric"=> "La cantidad del producto debe ser numérica",
            "detalles.*.montoDescuento.numeric"=> "El monto de descuento debe ser numérico"
        ];
    
        $response = validator($this->data, $rules, $messages);
        
        if (sizeof($response->errors()) > 0 ){
            
            throw new ClientFelException(json_encode($response->errors())) ;
        }


    }

    public function setCuf($cuf) 
    {
        $this->cuf = $cuf;
    }

    public function getInvoiceByCuf()
    {
        if ( empty($this->cuf) ) {
            throw new ClientFelException("Es necesario el cuf para obtener los detalles de la factura");
        }

        try {

            $response = $this->client->request('GET', "/api/v1/facturas/$this->cuf", ["headers" => ["Authorization" => "Bearer " . $this->access_token]]);

            return $this->parse_response($response);
        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            throw new ClientFelException("Error en la homologacion del producto: " . $ex->getMessage());
        }
    }

    public function buildData($model) {

        try{

        Log::debug("check model: " . json_encode($model));
        
        $client = $model->client;

        $user = $model->user;
    
        $line_items = $model->line_items;

        $total = 0;

        foreach($line_items as $detail) {
            
            $new = new stdClass;

            $hashid = new Hashids(config('ninja.hash_salt'),10);

            $new->codigoProducto =  $hashid->decode($detail->product_id)[0] .""; // this values was added only frontend Be careful
            $new->descripcion = $detail->notes;
            $new->precioUnitario = $detail->cost;
            $new->subTotal = $detail->quantity * $detail->cost;
            $new->cantidad = $detail->quantity;
            $new->numeroSerie =null;
            if ($detail->discount > 0)
                $new->montoDescuento =$detail->discount;
            $new->numeroImei =null;
            $new->unidadMedida = $detail->custom_value2;
            $details[] = (array)$new;

            $total += $new->subTotal;

        }

        $dateISOFormatted = substr( Carbon::parse($model->date)->format('Y-m-d\TH:i:s.u'), 0, -3);

        $data = [

            "numeroFactura" => $model->number,
            "codigoPuntoVenta" => 1,
            "fechaEmision" => $dateISOFormatted,
            "nombreRazonSocial" => $client->name,
            "codigoTipoDocumentoIdentidad" => $client->custom_value1,
            "numeroDocumento" => $client->id_number,
            "complemento" => null,
            "codigoCliente" => $client->id."",
            "codigoMetodoPago" => $model->custom_value3,
            "numeroTarjeta" => null,
            "montoTotal" => $total,
            "codigoMoneda" => 1,
            "montoTotalMoneda" => $total,
            "usuario" => $user->first_name . " " . $user->last_name,
            "emailCliente" => null,
            "telefonoCliente" => $client->phone,
            "extras" => null,
            "codigoLeyenda" => $model->custom_value1,
            "montoTotalSujetoIva" => $total,
            "tipoCambio" => 1 ,
            "detalles" => $details      
        ];

        Log::debug(" build invoice to send: .... " . json_encode($data));
        $this->setData($data);

        } catch(Exception $ex) {
            
            Log::error($ex->getMessage());

            throw new ClientFelException("Ocurrio un problema al construir los datos para enviar a FEL");
        }
        
    }
}
