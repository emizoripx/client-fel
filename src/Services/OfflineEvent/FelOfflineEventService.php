<?php

namespace EmizorIpx\ClientFel\Services\OfflineEvent;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\BaseConnection;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use Exception;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;

class FelOfflineEventService extends BaseConnection
{
    protected $access_token;

    protected $data;

    protected $response;

    protected $host;

    protected $branch_code;

    protected $type_document;

    protected $significant_event_code;

    public function __construct($host)
    {
        parent::__construct($host);
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    public function setBranchCode( $value ) {
        $this->branch_code = $value;
    }

    public function setTypeDocument( $value ) {

        $this->type_document = TypeDocumentSector::getFelDocumentNameByCode( $value ) ;

    }

    public function setSignificantEventCode( $value ) {

        $this->significant_event_code = $value;

    }

    public function setData($data)
    {
        $this->data  = $data;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function createSignificantEvent()
    {

        $this->checkParameters();

        try {
            
            \Log::debug("Send to : " . "/api/v1/fuera-de-linea/evento-significativo" );
            \Log::debug("data : " . json_encode($this->data));
            $response = $this->client->request('POST', "/api/v1/fuera-de-linea/evento-significativo", ["json" => $this->data, "headers" => ["Authorization" => "Bearer " . $this->access_token]]);
            $parsed_response = $this->parse_response($response);
            $this->setResponse($parsed_response);
            return $parsed_response;
        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            throw new ClientFelException("Error en la creación de la factura: " . $ex->getMessage());
        }
    }

    public function closeSignificantEvent() {

        try {

            if (empty($this->significant_event_code)) {
                throw new ClientFelException("El access token es necesario");
            }
            \Log::debug("Send to : " . "/api/v1/fuera-de-linea/evento-significativo/$this->significant_event_code/cerrar" );

            $response = $this->client->request("GET", "/api/v1/fuera-de-linea/evento-significativo/$this->significant_event_code/cerrar", ["headers" => ["Authorization" => "Bearer " . $this->access_token]]);

            $parsed_response = json_decode( (string) $response->getBody(), true);

            return $parsed_response;

        } catch(RequestException $re) {
            // \Log::debug("Request Exception: " . Psr7\Message::toString($re->getResponse()));
            \Log::debug("Request Exception: " . $re->getResponse()->getBody());
            $parsed_response = json_decode( (string) $re->getResponse()->getBody(), true);
            
            return $parsed_response;
        }
        catch (\Exception $ex) {

            Log::error($ex->getMessage());
            Log::error("Type Exception: " . gettype($ex));

            throw new ClientFelException("Error en el envio de paquetes: " . $ex->getMessage());
        }

    }

    public function getSignificantEventStatus () {

        try {

            if (empty($this->significant_event_code)) {
                throw new ClientFelException("El access token es necesario");
            }
            \Log::debug("Send to : " . "/api/v1/fuera-de-linea/evento-significativo/$this->significant_event_code/status" );

            $response = $this->client->request("GET", "/api/v1/fuera-de-linea/evento-significativo/$this->significant_event_code/status", ["headers" => ["Authorization" => "Bearer " . $this->access_token]]);

            $parsed_response = json_decode( (string) $response->getBody(), true);

            return $parsed_response;

        } catch(RequestException $re) {
            // \Log::debug("Request Exception: " . Psr7\Message::toString($re->getResponse()));
            \Log::debug("Request Exception: " . $re->getResponse()->getBody());
            $parsed_response = json_decode( (string) $re->getResponse()->getBody(), true);
            
            return $parsed_response;
        }
        catch (\Exception $ex) {

            Log::error($ex->getMessage());
            Log::error("Type Exception: " . gettype($ex));

            throw new ClientFelException("Error en el envio de paquetes: " . $ex->getMessage());
        }

    }

    public function sendPackageToFel() {

        $this->checkParameters();

        try {
            
            \Log::debug("Send to : " . "/api/v1/fuera-de-linea/facturas/$this->branch_code/$this->type_document" );
            \Log::debug("data : " . json_encode($this->data));
            $response = $this->client->request('POST', "/api/v1/fuera-de-linea/facturas/$this->branch_code/$this->type_document", ["multipart" => $this->data, "headers" => ["Authorization" => "Bearer " . $this->access_token]]);
            $parsed_response = $this->parse_response($response);
            $this->setResponse($parsed_response);
            return $parsed_response;
        } catch(RequestException $re) {
            // \Log::debug("Request Exception: " . Psr7\Message::toString($re->getResponse()));
            \Log::debug("Request Exception: " . $re->getResponse()->getBody());
            $parsed_response = $this->parse_response($re->getResponse());
            $this->setResponse($parsed_response);
            return $parsed_response;
        }
         catch (\Exception $ex) {

            Log::error($ex->getMessage());
            Log::error("Type Exception: " . gettype($ex));

            throw new ClientFelException("Error en el envio de paquetes: " . $ex->getMessage());
        }

    }

    public function checkParameters()
    {
        if (empty($this->access_token)) {
            throw new ClientFelException("El access token es necesario");
        }

        if (empty($this->data)) {
            throw new ClientFelException("Los datos son necesarios para enviar.");
        }
    }


    public function createJsonFile( $invoices ) {

        $id = uniqid();
        $filename = "Invoices_$id.json";

        if( ! is_dir(storage_path('app/packages/files')) ) {
            \Log::debug("Create diretory Templates");
            mkdir(storage_path('app/packages/files'), 0777, true);
        }

        $handle = fopen(storage_path("app/packages/files/$filename"), 'w+');
        fputs($handle, $invoices->toJson(JSON_PRETTY_PRINT));
        fclose($handle);

        return storage_path("app/packages/files/$filename");
    }

    public function addZipFile( $file_path ) {

        if( ! is_dir(storage_path('app/packages/compress')) ) {
            \Log::debug("Create diretory Templates");
            mkdir(storage_path('app/packages/compress'), 0777, true);
        }

        $id = $id = uniqid();
        $zip_name = "Invoices_$id.zip";
        $file_name = "Invoices_$id.json";

        $zip = new ZipArchive;

        $res = $zip->open( storage_path("app/packages/compress/$zip_name"), ZipArchive::CREATE);
        if( $res === TRUE ) {

            \Log::debug("File Path: " . $file_path);
            $zip->addFile( $file_path, $file_name);

            $zip->close();

            return storage_path("app/packages/compress/$zip_name");

        } else {
            \Log::debug("Error al Crear archivo ZIP: " . $res);

            throw new Exception("No se pudo crear el archivo ZIP: " . $res);
        }


    }

}
