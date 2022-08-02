<?php
namespace EmizorIpx\ClientFel\Services;

use GuzzleHttp\Client;

class BaseConnection {

    public function __construct($host)
    {
        \Log::debug("HOST: $host");
        $data['base_uri'] = $host;
        $data['headers']['Accept'] = 'application/json';
        $data['headers']['Content-Type'] = 'application/json';

        $this->client = new Client($data);
    }

    public function parse_response($response)
    {
        $response = json_decode( (string) $response->getBody(), true);
        \Log::debug("response : " . json_encode($response));
        if ( isset($response['status']) ) {

            if ($response['status'] != 'success' ) {
                
                if( isset( $response['errors'] ) ) {

                    return $response['errors'];
                    
                }
            }

            return $response['data'];
        }else {

            return $response;

        }
    }
}