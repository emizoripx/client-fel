<?php

namespace EmizorIpx\ClientFel\Services\Whatsapp;

use EmizorIpx\ClientFel\Exceptions\WhatsappException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Whatsapp {

    protected $number;

    protected $botId;

    protected $client;

    protected $data;

    protected $prepared_data;

    const QUEUED = 'queued';
    const DISPATCHED = 'dispatched';
    const SENT = 'sent';
    const DELIVERED = 'delivered';
    const READ = 'read';
    const DELETED = 'deleted';
    const FAILED = 'failed';
    const NO_OPT_IN = 'no_opt_in';
    const NO_CAPABILITY = 'no_capability';

    public function __construct()
    {
        \Log::debug("Data Cliente >>>>>>>>>>> " . 'Bearer ' . config('clientfel.token_whatsapp'));
        $data_client['base_uri'] = config('clientfel.host_whatsapp');
        $data_client['headers']['Authorization'] = 'Bearer ' . config('clientfel.token_whatsapp');
        $data_client['headers']['Content-Type'] = 'application/json';

        \Log::debug("Data Cliente >>>>>>>>>>> " . json_encode($data_client) );

        $this->client = new Client($data_client);

        $this->botId = config('clientfel.bot_id_whatsapp');
    }

    public function setNumber($number){
        \Log::debug("Set NUmber " . $number);

        $this->number = $number;

        \Log::debug($this->number);
    }

    public function setData ($data){
        \Log::debug("Data to set");
        \Log::debug(json_encode($data));
        $this->data = $data;
        \Log::debug(json_encode($this->data));
    }

    public function parse_response($response){
        
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 201 ){

            return  json_decode( (string) $response->getBody(), true);

        } else if( $response->getStatusCode() == 400 ){
            \Log::debug("Bad Request");
            throw new WhatsappException( json_encode(["errors" => [" 400: Bad Request "]]));

        } else if( $response->getStatusCode() == 401 ){
            \Log::debug("401: Unauthorized");
            throw new WhatsappException( json_encode(["errors" => [" 401: Unauthorized"]]));
        }

    }


    public function prepared_data(){
        $bucket = config('clientfel.s3_bucket');

        \Log::debug("https://$bucket.s3.amazonaws.com/" . $this->data['pdf_url']);

        $this->prepared_data = [
            "to" => [$this->number],
            "message" => [
                "type" => "template",
                "template_name" => "emizor_factura_3",
                "language" => "es",
                "body_params" => [$this->data['nit'], $this->data['company_name'], "Bs ". number_format($this->data['monto_total'], 2)],
                "media" => [
                    "type" => "document",
                    "url" => "https://$bucket.s3.amazonaws.com/" . $this->data['pdf_url'],
                    "filename" => $this->data['pdf_name']
                ],
                "buttons" => [[
                    "type" => "url",
                    "parameter" => $this->data['contact_key']
                ]]
            ],
            "callback" => config('clientfel.callback_url_whatsapp')
        ];
    }

    public function checkParameters(){
        if(empty($this->botId)){
            throw new WhatsappException( json_encode(["errors" => ["Bot ID Requerido"]]));
        }
        if(empty($this->number)){
            throw new WhatsappException( json_encode(["errors" => ["Número de Teléfono es requerido"]]));
        }
        if(empty($this->data)){
            throw new WhatsappException( json_encode(["errors" => ["Datos requeridos para el envío"]]));
        }
    }

    public function authorizationOfSending(){

        \Log::debug("Authorize <<<<<<<<<<< ". $this->number);
        
        $body = [ "numbers" => [ $this->number ]];
        
        \Log::debug("Authorize <<<<<<<<<<< " . json_encode($body));

        try{
            \Log::debug("/whatsapp/v1/$this->botId/provision/optin");
            $response = $this->client->request('POST', "/whatsapp/v1/$this->botId/provision/optin", [ "json" => $body ] );

            \Log::debug("Response Authorization >>>>>>>>>>>>>> ");
            $response = $this->parse_response($response);

            \Log::debug("Response Authorization  " . json_encode($response));

            if( sizeof($response['failedToOptInNumbers']) > 0 ){
                // \Log::debug("No se tiene autorización para el envio del Mensaje envio al numero  : " . $response['failedToOptInNumbers'][0]['msisdn'] . " Razón: " . $response['failedToOptInNumbers'][0]['rejectionReason']);
                return [false, $response['failedToOptInNumbers'][0]];
            }

            return [true, null];


        } catch(RequestException $ex){
            \Log::debug("Error Connection Authorize ");
            \Log::debug($ex->getResponse()->getBody());

            throw new WhatsappException( json_encode(["errors" => [json_decode ($ex->getResponse()->getBody())]]) );
        }

    }

    public function CancelAuthorization(){
        $body = [ "numbers" => [ $this->number ]];

        try{

            $response = $this->client->request('DELETE', "/whatsapp/v1/$this->botId/provision/optin", [ "json" => $body ] );

            $response = $this->parse_response($response);

            return $response;

        } catch( RequestException $ex ){

            throw new WhatsappException( json_encode (["errors" => [ json_decode ($ex->getResponse()->getBody())]]) );
        }

    }

    public function sendMessage (){
        
        $this->checkParameters();

        $this->prepared_data();

        \Log::debug("Data to sent >>>>>>>>> " . json_encode($this->prepared_data));

        try{

            $response = $this->client->request( 'POST', "/whatsapp/v1/$this->botId/messages", ["json" => $this->prepared_data] );

            $parsed_response = $this->parse_response($response);

            \Log::debug("Response Send Message >>>>>>>>>>>>>> " . json_encode($parsed_response) );

            return $parsed_response;
        
        } catch(RequestException $ex){

            \Log::debug("Error al enviar el mensaje ". $ex->getResponse()->getBody());

            throw new WhatsappException( json_encode(["errors" => [ json_decode ($ex->getResponse()->getBody())]]) );

        }

    }

}
