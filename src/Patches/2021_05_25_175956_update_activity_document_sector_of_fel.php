<?php

use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\Log;
use EmizorIpx\ClientFel\Utils\TypeParametrics;

class UpdateActivityDocumentSectorOfFel
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        $felTokens = FelClientToken::all();

        foreach ($felTokens as $feltoken) {
            $parametricService = new Parametric($feltoken->access_token, $feltoken->host);
            
            try {
                sleep(2);
                if (FelParametric::existsParametric(TypeParametrics::ACTIVIDADES_DOCUMENTO_SECTOR, $feltoken->account_id)) {
                    $parametricService->get(TypeParametrics::ACTIVIDADES_DOCUMENTO_SECTOR);
                    FelParametric::create(TypeParametrics::ACTIVIDADES_DOCUMENTO_SECTOR, $parametricService->getResponse(), $feltoken->account_id);


                    \Log::debug("Actividades documento sector actualizadas de Company #" .$feltoken->account_id);
                }
            } catch (Exception $ex) {
                \Log::debug("No se pudo obtener las actividades documento sector de Company #" .$feltoken->account_id);
            }
        }
    }
}
