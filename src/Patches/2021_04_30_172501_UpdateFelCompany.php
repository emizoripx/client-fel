<?php

use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Services\Company\Company;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;

class UpdateFelCompany
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        
        $felCompanyTokens = FelClientToken::get();

        foreach( $felCompanyTokens as $companyToken ){

            $this->updateCompany($companyToken);

        }

    }


    public function updateCompany($companyToken){

        
        try {
            
            $felCompany = AccountPrepagoBags::where('company_id', $companyToken->account_id)->first();
            
            if( !is_null($felCompany) ){
                
                $companyService = new Company($companyToken->access_token, $companyToken->host);

                $response =  $companyService->getCompany();

                
                $felCompany->update([
                    'fel_company_id' => $response['id']
                ]);
                    
                \Log::debug(json_encode($felCompany));
            }
            

        } catch (Exception $ex) {
            \Log::debug("Error al actualizar datos de FelCompany ". $ex->getMessage());
        }
    }
}
