<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Connection\Connection;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Exception;
use Illuminate\Console\Command;

class GetSystemCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emizor:get-system-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update System Codes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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
    
                    $this->info("Empresa # $company->company_id  was updated sector document types : " . " con host  " . $company->fel_company_token->getHost());
                } else {
                    \Log::debug("NO SE ENCONTRO CREDENCIALES");
                    $this->info('No se encontró credenciales para la compañía ID: ' . $company->company_id);
                }
                
            } catch (Exception $ex) {
                $this->warn("NO SE PUEDE AUTENTICAR LA EMPRESA # " . $company->company_id  .  " con host  " . $company->fel_company_token->getHost() .' ERROR: '. $ex->getMessage());
            }
        });
    }
}
