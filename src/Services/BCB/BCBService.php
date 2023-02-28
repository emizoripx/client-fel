<?php

namespace EmizorIpx\ClientFel\Services\BCB;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Exceptions\WhatsappException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BCBService {

    protected $client;

    protected $data;

    protected $prepared_data;

    protected $start_date;

    protected $end_date;

    public function __construct()
    {
        \Log::debug("Data Cliente >>>>>>>>>>> " . 'Bearer ');
        $data_client['base_uri'] = config('clientfel.bcb_host');
        $data_client['headers']['Content-Type'] = 'application/json';


        $this->client = new Client($data_client);
    }

    public function setData ($data){
        \Log::debug("Data to set");
        \Log::debug(json_encode($data));

        $this->data = $data;
    }

    public function setStartDate( $value ) {

        $this->start_date = $value;

    }

    public function setEndDate( $value ) {

        $this->end_date = $value;

    }

    public function parse_response($response){
        
        if($response->getStatusCode() == 200){

            return  json_decode( (string) $response->getBody(), true);

        } else if( $response->getStatusCode() == 400 ){
            \Log::debug("Bad Request");
            throw new ClientFelException( " 400: Bad Request " );

        } else if( $response->getStatusCode() == 401 ){
            \Log::debug("401: Unauthorized");
            throw new ClientFelException( " 401: Unauthorized" );
        }

    }

    public function checkParameters(){
        if(empty($this->start_date)){
            throw new ClientFelException("Fecha Inicio Requerido");
        }
        if(empty($this->end_date)){
            throw new ClientFelException( "Fecha Final Requerido" );
        }
    }

    /**
     * @return array
     */
    public function getUfvValue (){
        
        $this->checkParameters();

        try{

            $response = $this->client->request( 'GET', "/librerias/charts/ufv.php?cFecIni=" . $this->start_date . "&cFecFin=". $this->end_date );

            $parsed_response = $this->parse_response($response);

            \Log::debug("Response Send Message >>>>>>>>>>>>>> " . json_encode($parsed_response) );

            return $parsed_response;
        
        } catch(RequestException $ex){

            \Log::debug("Error al enviar el mensaje ". $ex->getResponse()->getBody());

            throw new WhatsappException( json_encode(["errors" => [ json_decode ($ex->getResponse()->getBody())]]) );

        }

    }

}
