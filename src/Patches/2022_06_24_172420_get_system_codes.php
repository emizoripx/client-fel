<?php

use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;

class GetSystemCodes
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        
        AccountPrepagoBags::cursor()->each(function ($company) {
            
            // $connection = new Connection($felClienttoken->getHost());

            // $clientId = $felClienttoken->getClientId();
            // $clientSecret = $felClienttoken->getClientSecret();


            // $data = [
            //     "grant_type" => "client_credentials",
            //     "client_id" => $clientId,
            //     "client_secret" => $clientSecret
            // ];
            try {
                if(isset($company->fel_company_token)) {

                    sleep(1);
    
                    $parametricService = new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());
    
                    \Log::debug("GET DOCUMENTO SECTOR");
                    $parametricService->get(TypeParametrics::TIPOS_DOCUMENTO_SECTOR, '', true);
    
                    \Log::debug("TIPOS DOCUMENTO SECTOR");
                    FelParametric::saveParametrics(TypeParametrics::TIPOS_DOCUMENTO_SECTOR, $company->company_id, $parametricService->getResponse());
    
                    \Log::debug("Empresa # $company->company_id  was updated sector document types : " . " con host  " . $company->fel_company_token->getHost());
                } else {
                    \Log::debug("NO SE ENCONTRO CREDENCIALES");
                    \Log::debug('No se encontró credenciales para la compañía ID: ' . $company->company_id);
                }
                
            } catch ( \Exception $ex) {
                \Log::debug("NO SE PUEDE AUTENTICAR LA EMPRESA # " . $company->company_id  .  " con host  " . $company->fel_company_token->getHost() .' ERROR: '. $ex->getMessage());
            }
        });

    }
}
