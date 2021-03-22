<?php

namespace EmizorIpx\ClientFel\Services\Invoices;

use Carbon\Carbon;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
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

    protected $host;

    public function __construct($host)
    {
        parent::__construct($host);
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
            \Log::debug(json_encode($this->data));
            \Log::debug(json_encode($this->client));
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

            throw new ClientFelException("Error al obtener detalles de la factura: " . $ex->getMessage());
        }
    }

    public function buildData(FelInvoiceRequest $model) {

        try{

            $this->setData($model->toArray());

        } catch(Exception $ex) {
            
            Log::error($ex->getMessage());

            throw new ClientFelException("Ocurrio un problema al construir los datos para enviar a FEL");
        }
        
    }

    public function setRevocationReasonCode($code){
        $this->revocationReasonCode = $code;
    }

    public function revocateInvoice(){
        if(empty($this->cuf)){
            throw new ClientFelException("Es necesario el cuf para anular la factura");
        }
        \Log::debug('Motivo de Anulacion '. $this->revocationReasonCode);
        try {
            $response = $this->client->request('DELETE', "/api/v1/facturas/$this->cuf/anular?codigoMotivoAnulacion=$this->revocationReasonCode", ["headers" => ["Authorization" => "Bearer " . $this->access_token]]);
            
            return $this->parse_response($response);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            throw new ClientFelException("Error al anular la factura ". $ex->getMessage());
        }
    }
}
