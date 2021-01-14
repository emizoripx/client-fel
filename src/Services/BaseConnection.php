<?php
namespace EmizorIpx\ClientFel\Services;

use GuzzleHttp\Client;

class BaseConnection {

    public function __construct()
    {
        $data['base_uri'] = config('clientfel.api_url');
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
                
                if ($response['ms_error']) {
                   

                } elseif ($response['sin_errors']) {
                   

                }
            }

            return $response['data'];
        }else {

            return $response;

        }
    }
}