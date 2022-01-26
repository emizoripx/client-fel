<?php

use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;

class SyncRoomTypes
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        $host_env = getenv('APP_URL');
        $fel_host = null;
        if($host_env == 'https://felapp.emizor.com'){
            
            $fel_host = 'https://sinfel.emizor.com';

        } elseif ($host_env == 'https://web.emizor.com'){

            $fel_host = 'https://fel.emizor.com';
        }
        
        $company_token = FelClientToken::where('host', $fel_host)->whereNotNull('access_token')->first();

        if($company_token){

            \Log::debug("Sincronizar TIPOS DE HABITACIÓN >>>>>>>>>>>>>> Host: ". $company_token->host);
            
            $parametric_service = new Parametric($company_token->access_token, $company_token->host);
            
            if (FelParametric::existsParametric(TypeParametrics::TIPOS_HABITACION, $company_token->account_id)) {
                \Log::debug("Sincronizar >>>>>>>>>>>>>>");
                $parametric_service->get(TypeParametrics::TIPOS_HABITACION);
                
                FelParametric::create(TypeParametrics::TIPOS_HABITACION, $parametric_service->getResponse());
            }
            \Log::debug("Sincronización Terminada TIPOS DE HABITACIÓN >>>>>>>>>>>>>> Host: ". $company_token->host);

        } else{
            \Log::debug("No se encontro Host para Sincronizar >>>>>>>>>>>>>>");
        }
    }
}
