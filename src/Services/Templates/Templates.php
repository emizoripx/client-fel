<?php

namespace EmizorIpx\ClientFel\Services\Templates;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Services\BaseConnection;
use Exception;

class Templates extends BaseConnection {

    protected $accessToken;

    protected $response;
    
    protected $host;

    public function __construct($accessToken, $host)
    {
        $this->accessToken = $accessToken;

        parent::__construct($host);
    }


    public function getTemplates(){
        try{
            $response = $this->client->request('GET', '/api/v1/templates', ["headers" => ["Authorization" => "Bearer " . $this->accessToken]]);

            return $this->parse_response($response);

        } catch(Exception $ex){

            \Log::error($ex->getMessage());

            throw new ClientFelException("Error a listar las Templates " . $ex->getMessage());

        }
    }

    public function exitsTemplate( $company_id, $document_sector, $branch_code ) {


        $template = \DB::table('fel_templates')->where('company_id', $company_id)->where('document_sector_code', $document_sector)->where('branch_code', $branch_code)->first();

        return is_null($template);

    }

}