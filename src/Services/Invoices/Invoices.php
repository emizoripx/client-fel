<?php

namespace EmizorIpx\ClientFel\Services\Invoices;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Services\BaseConnection;
use EmizorIpx\ClientFel\Services\FelConnection;
use EmizorIpx\ClientFel\Services\Invoices\Resources\TypeDocumentResource;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
class Invoices extends FelConnection
{
    protected $access_token;

    protected $data;

    protected $data_model;

    protected $type_document;

    protected $branch_number;

    protected $host;

    protected $prepared_data;

    protected $ack_ticket;


    public function __construct($host, $access_token)
    {
        parent::__construct($host, $access_token);
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }


    public function setData($data)
    {
        $this->data  = $data;
    }

    public function setDataModel($data)
    {
        $this->data_model  = $data;
    }

    public function setTypeDocument()
    {
        $this->type_document = TypeDocumentSector::getFelDocumentNameByCode($this->data['type_document_sector_id']);
    }

    public function setBranchNumber($branch_number)
    {
        $this->branch_number = $branch_number;
    }

    public function sendToFel()
    {

        $this->checkParameters();

        $this->setTypeDocument();

        $this->prepareData();

        $this->emit($this->prepared_data, $this->type_document);
        $this->prepared_data = "";

    }

    public function checkParameters()
    {
        if (empty($this->access_token)) {
            throw new ClientFelException("El access token es necesario");
        }

        if ($this->branch_number < 0) {
            throw new ClientFelException("El branch_number es necesario");
        }

        if (empty($this->data)) {
            throw new ClientFelException("Los datos son necesarios para enviar.");
        }
    }

    public function setCuf($cuf) 
    {
        $this->cuf = $cuf;
    }
    public function setAckTicket($ack_ticket) 
    {
        $this->ack_ticket = $ack_ticket;
    }

    public function getInvoiceByCuf()
    {
        if ( empty($this->cuf) ) {
            throw new ClientFelException("Es necesario el cuf para obtener los detalles de la factura");
        }

        try {
            \Log::debug("Send to : " ."/api/v1/facturas/$this->cuf" );
            $response = $this->client->request('GET', "/api/v1/facturas/$this->cuf", ["headers" => ["Authorization" => "Bearer " . $this->access_token]]);
            $parsed_response = $this->parse_response($response);
            $this->setResponse($parsed_response);
            return $parsed_response;
        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            throw new ClientFelException("Error al obtener detalles de la factura: " . $ex->getMessage());
        }
    }

    public function getStatusByAckTicket()
    {
        \Log::debug("CHECKING STATUS OF INVOICE=..>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>####################################");
        if (empty($this->ack_ticket)) {
            \Log::debug("CHECKING STATUS OF INVOICE=..>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>####################################  1");
            throw new ClientFelException("Es necesario el ackticket para obtener los detalles de la factura");
        }

        try {
            \Log::debug("CHECKING STATUS OF INVOICE=..>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>####################################  2");
            \Log::debug("Send to : " . "/api/v1/facturas/$this->ack_ticket/status");
            $response = $this->client->request('GET', "/api/v1/facturas/$this->ack_ticket/status", ["headers" => ["Authorization" => "Bearer " . $this->access_token]]);
            $parsed_response = $this->parse_response($response);
            $this->setResponse($parsed_response);
            \Log::debug("CHECKING STATUS OF INVOICE=..>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  --------------------------");
            return $parsed_response;
        } catch (\Exception $ex) {
            \Log::debug("CHECKING STATUS OF INVOICE=..>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>####################################  3");
            Log::error($ex->getMessage());

            throw new ClientFelException("Error al obtener estado de la factura: " . $ex->getMessage());
        }
    }

    public function getInvoiceByAckTicket()
    {
        if ( empty($this->ack_ticket) ) {
            $this->getInvoiceByCuf();
        }

        try {
            \Log::debug("Send to : " ."/api/v2/facturas/$this->ack_ticket" );
            $response = $this->client->request('GET', "/api/v2/facturas/$this->ack_ticket", ["headers" => ["Authorization" => "Bearer " . $this->access_token]]);
            $parsed_response = $this->parse_response($response);
            $this->setResponse($parsed_response);
            return $parsed_response;
        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            throw new ClientFelException("Error al obtener detalles de la factura: " . $ex->getMessage());
        }
    }

    public function buildData(FelInvoiceRequest $model) {

        try{

            $this->setData($model->toArray());
            $this->setDataModel($model);

        } catch(Exception $ex) {
            
            Log::error($ex->getMessage());

            throw new ClientFelException("Ocurrio un problema al construir los datos para enviar a FEL");
        }
        
    }

    public function setRevocationReasonCode($code){
        $this->revocationReasonCode = $code;
    }

    public function revocateInvoice($factura_ticket)
    {
        $this->revocate($factura_ticket, $this->revocationReasonCode );
    }

    public function reversionRevocateInvoice(){

        
    }

    public function updateInvoice($factura_ticket){
      
        $this->checkParameters();

        $this->setTypeDocument();

        $this->prepareData();

        $this->remit($this->prepared_data, $this->type_document,$factura_ticket);
        
        $this->prepared_data = "";
    }

    public function prepareData()
    {
        
        try{
            $this->prepared_data =  new TypeDocumentResource($this->data_model);
            \Log::debug("Data");
            \Log::debug(json_encode($this->prepared_data));
        }catch(Throwable $th) {
            \Log::error($th);
        }
    }

    public function validateNit($nit)
    {
        $this->checkNit($nit);
    }

    public function verifyStatus()
    {
        try {
            $response = $this->client->request('GET', "/api/v1/sucursales/0/validate-nit/$nit", [ "headers" => ["Authorization" => "Bearer " . $this->access_token]]);
            $parsed_response = $this->parse_response($response);
            $this->setResponse($parsed_response);
            return $this->parse_response($response);
        } catch (MaintenanceModeException $ex) {
            Log::error($ex->getMessage());
            throw new ClientFelException("El servicio FEL está en mantenimiento, espere por favor.");
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            throw new ClientFelException("Error al validar el NIT");
        }
    }

 
    public function getErrors()
    {
        info(">>>>>>>>>>>>>>>> ERRRORS  =>>>>>  " , [$this->errors]);
        if (empty($this->errors)) {
            return [];
        }else {

            $built = array();
            foreach ($this->errors as $errname => $value) {
                if ( is_array($value)) {

                    foreach ($value as $v) {
                        $array = $errname . " => " . $v;
                    }
                } else {
                    $array = $value;;
                }
                array_push($built, ["description" => $array]);
            }

            return $built;
        }
    
    }

}
