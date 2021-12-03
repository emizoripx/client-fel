<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Illuminate\Http\Request;

class WebhookParametrics extends BaseController
{

    public function updateParametrics(Request $request)
    {

        \Log::debug("WEBHOOK PARAMETRICAS ------ Inicio");

        $data = $request->all();

        \Log::debug("WEBHOOK PARAMETRICAS ------ Datos");
        \Log::debug($data);

        \Log::debug("WEBHOOK PARAMETRICAS ------ Obtener Companies");

        if (!$data['is_general']) {
            $companies = AccountPrepagoBags::where('fel_company_id', $data['company_id'])->get();

            $companyProduction = $companies->where('phase', 'Production')->all();

            \Log::debug("WEBHOOK PARAMETRICAS ------ Actualizar companies Fase Produccción");
            // Sync Companies Production
            foreach ($companyProduction as $company) {
                $this->parametricSyncPhaseProduction($data['data'], $company);
            }
            \Log::debug("WEBHOOK PARAMETRICAS ------ Fin Actulizaci[on de companies Fase Producción");

            \Log::debug("WEBHOOK PARAMETRICAS ------ Actualizar companies Fase Testing");
            // Sync Company in phase Testing
            $companyTesting = $companies->whereIn('phase', ['Testing', 'Piloto testing'])->all();

            if (!empty($companyTesting)){

                $company = collect($companyTesting)->first();
                $parametricService = new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());
                \Log::debug("Company First");
                \Log::debug($company);
                foreach ($data['data'] as $parametric) {
                    \Log::debug("WEBHOOK PARAMETRICAS ------ Get " . $parametric);
                    $parametricService->get($parametric, FelParametric::getUpdatedAt($parametric, $company->company_id), true);
    
                    $this->parametricSyncPhaseTesting($parametric, $companyTesting, $parametricService->getResponse());
                }
            }


        }
        else{

            \Log::debug("WEBHOOK PARAMETRICAS ------ Generales");

            $company = AccountPrepagoBags::where('fel_company_id', $data['company_id'])->first();

            $parametricService = new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());

            foreach ($data['data'] as $parametric){
                \Log::debug("WEBHOOK PARAMETRICAS ------ Get " . $parametric);
                $parametricService->get($parametric, FelParametric::getUpdatedAt($parametric, $company->company_id), true);

                \Log::debug("WEBHOOK PARAMETRICAS ------ Guardar " . $parametric);
                FelParametric::saveParametrics($parametric, $company->company_id, $parametricService->getResponse());

            }

        }
    }

    public function parametricSyncPhaseProduction($parametricUpdate, $company)
    {
        $parametricService = new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());
        \Log::debug("WEBHOOK PARAMETRICAS ------ Company ID " . $company->company_id);
        foreach ($parametricUpdate as $parametric) {
            \Log::debug("WEBHOOK PARAMETRICAS ------ Get " . $parametric);
            $parametricService->get($parametric, FelParametric::getUpdatedAt($parametric, $company->company_id), true);

            \Log::debug("WEBHOOK PARAMETRICAS ------ Guardar " . $parametric);
            FelParametric::saveParametrics($parametric, $company->company_id, $parametricService->getResponse());
        }
    }

    public function parametricSyncPhaseTesting($type, $companies, $data)
    {

        foreach ($companies as $company) {
            \Log::debug("WEBHOOK PARAMETRICAS ------Actualizar Company ID " . $company->company_id);
            FelParametric::saveParametrics($type, $company->company_id, $data);
        }
    }


}
