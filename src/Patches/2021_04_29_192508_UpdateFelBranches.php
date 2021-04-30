<?php

use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\ClientFel\Models\FelClient;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Services\Branches\Branches;

class UpdateFelBranches
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        
        $felCompanyTokens = FelClientToken::get();

        \Log::debug("Tokens");
        \Log::debug([$felCompanyTokens]);

        foreach ($felCompanyTokens as $companyToken) {
            
            $this->syncBranches($companyToken, $companyToken->host);

        }

    }

    public function syncBranches($companyToken, $host){

        \Log::debug("Token: ".$companyToken->access_token. '  Host: '.$host);

        try {
            $branchService = new Branches($companyToken->access_token, $host);

            $branches = $branchService->getBranches();

            foreach($branches as $branch){
                \Log::debug($branch);;

                    $branch = FelBranch::where('company_id', $companyToken->account_id)->where('codigo', $branch['codigoSucursal'])->update([
                        'codigo' => $branch['codigoSucursal'],
                        'descripcion' => $branch['codigoSucursal'] == 0 ? 'Casa Matriz' : 'Sucursal '.$branch['codigoSucursal'],
                        'company_id' => $companyToken->account_id,
                        'zona' => $branch['zona'],
                        'pais' => $branch['pais'],
                        'ciudad' => $branch['ciudad'],
                        'municipio' => $branch['municipio']
                    ]);
            }
        } catch (Exception $ex) {
            \Log::error("Problemas al Obtener los Datos de Sucursales ". $ex->getMessage());
        }

    }
}
