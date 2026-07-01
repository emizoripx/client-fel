<?php

namespace EmizorIpx\ClientFel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;

class SyncParametricsWebhookJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->data['company_id'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;

        \Log::info("JOB PARAMETRICAS ------ Inicio del flujo", ['data' => $data]);

        if (!$data['is_general']) {
            $companies = AccountPrepagoBags::where('fel_company_id', $data['company_id'])->get();
            \Log::info("JOB PARAMETRICAS ------ Se encontraron " . $companies->count() . " empresas para el fel_company_id: " . $data['company_id']);

            $companyProduction = $companies->where('phase', 'Production')->all();

            \Log::info("JOB PARAMETRICAS ------ Inicio actualización Fase Producción (" . count($companyProduction) . " empresas)");
            // Sync Companies Production
            foreach ($companyProduction as $company) {
                \Log::info("JOB PARAMETRICAS ------ Producción: Sincronizando empresa " . $company->company_id);
                $this->parametricSyncPhaseProduction($data['data'], $company);
            }
            \Log::info("JOB PARAMETRICAS ------ Fin actualización Fase Producción");

            \Log::info("JOB PARAMETRICAS ------ Inicio actualización Fase Testing");
            // Sync Company in phase Testing
            $companyTesting = $companies->whereIn('phase', ['Testing', 'Piloto testing'])->all();

            if (!empty($companyTesting)){
                \Log::info("JOB PARAMETRICAS ------ Se encontraron " . count($companyTesting) . " empresas en Testing");
                $company = collect($companyTesting)->first();
                $parametricService = new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());
                
                foreach ($data['data'] as $parametric) {
                    \Log::info("JOB PARAMETRICAS ------ Testing: Solicitando parametrica " . $parametric . " para empresa token: " . $company->company_id);
                    $parametricService->get($parametric, FelParametric::getUpdatedAt($parametric, $company->company_id), true);
                    $response = $parametricService->getResponse();
                    \Log::info("JOB PARAMETRICAS ------ Testing: Respuesta obtenida para " . $parametric, ['response_count' => is_array($response) ? count($response) : 'No es array']);
                    $this->parametricSyncPhaseTesting($parametric, $companyTesting, $response);
                }
            } else {
                \Log::info("JOB PARAMETRICAS ------ No se encontraron empresas en Testing");
            }

        }
        else{

            \Log::info("JOB PARAMETRICAS ------ Inicio actualización Generales (is_general=true)");

            $company = AccountPrepagoBags::where('fel_company_id', $data['company_id'])->first();

            if ($company) {
                \Log::info("JOB PARAMETRICAS ------ Generales: Usando token de empresa " . $company->company_id);
                $parametricService = new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());

                foreach ($data['data'] as $parametric){
                    \Log::info("JOB PARAMETRICAS ------ Generales: Solicitando parametrica " . $parametric);
                    $parametricService->get($parametric, FelParametric::getUpdatedAt($parametric, $company->company_id), true);
                    $response = $parametricService->getResponse();
                    \Log::info("JOB PARAMETRICAS ------ Generales: Guardando respuesta para " . $parametric, ['response_count' => is_array($response) ? count($response) : 'No es array']);
                    FelParametric::saveParametrics($parametric, $company->company_id, $response);
                }
            } else {
                \Log::info("JOB PARAMETRICAS ------ Generales: No se encontró AccountPrepagoBags para fel_company_id: " . $data['company_id']);
            }
        }
        
        \Log::info("JOB PARAMETRICAS ------ Fin del flujo completo");
    }

    public function parametricSyncPhaseProduction($parametricUpdate, $company)
    {
        \Log::info("JOB PARAMETRICAS ------ Producción: Iniciando request para empresa " . $company->company_id);
        $parametricService = new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());
        
        foreach ($parametricUpdate as $parametric) {
            \Log::info("JOB PARAMETRICAS ------ Producción: Solicitando parametrica " . $parametric . " para empresa " . $company->company_id);
            $parametricService->get($parametric, FelParametric::getUpdatedAt($parametric, $company->company_id), true);
            $response = $parametricService->getResponse();
            \Log::info("JOB PARAMETRICAS ------ Producción: Guardando respuesta para " . $parametric . " de empresa " . $company->company_id, ['response_count' => is_array($response) ? count($response) : 'No es array']);
            FelParametric::saveParametrics($parametric, $company->company_id, $response);
        }
    }

    public function parametricSyncPhaseTesting($type, $companies, $data)
    {
        foreach ($companies as $company) {
            \Log::info("JOB PARAMETRICAS ------ Testing: Guardando parametrica " . $type . " para empresa " . $company->company_id, ['data_count' => is_array($data) ? count($data) : 'No es array']);
            FelParametric::saveParametrics($type, $company->company_id, $data);
        }
    }
}
