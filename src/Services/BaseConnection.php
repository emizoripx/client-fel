<?php
namespace EmizorIpx\ClientFel\Services;

use GuzzleHttp\Client;

class BaseConnection {

    public function __construct($host, $accessToken = null, $tenant_key = null)
    {
        \Log::debug("HOST: $host");
        $data['base_uri'] = $host;
        $data['headers']['Accept'] = 'application/json';
        $data['headers']['Content-Type'] = 'application/json';
        $data['headers']['emizor-header'] = 'true';

        if (!empty($accessToken)) {
            $data['headers']['Authorization'] = "Bearer " . $accessToken;
        }
        if (!empty($tenant_key)) {
            $data['headers']['tenant-key'] = $tenant_key;
        }

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