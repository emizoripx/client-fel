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

        \Log::debug("JOB PARAMETRICAS ------ Inicio");

        if (!$data['is_general']) {
            $companies = AccountPrepagoBags::where('fel_company_id', $data['company_id'])->get();

            $companyProduction = $companies->where('phase', 'Production')->all();

            \Log::debug("JOB PARAMETRICAS ------ Actualizar companies Fase Produccción");
            // Sync Companies Production
            foreach ($companyProduction as $company) {
                $this->parametricSyncPhaseProduction($data['data'], $company);
            }
            \Log::debug("JOB PARAMETRICAS ------ Fin Actulizaci[on de companies Fase Producción");

            \Log::debug("JOB PARAMETRICAS ------ Actualizar companies Fase Testing");
            // Sync Company in phase Testing
            $companyTesting = $companies->whereIn('phase', ['Testing', 'Piloto testing'])->all();

            if (!empty($companyTesting)){

                $company = collect($companyTesting)->first();
                $parametricService = new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());
                
                foreach ($data['data'] as $parametric) {
                    $parametricService->get($parametric, FelParametric::getUpdatedAt($parametric, $company->company_id), true);
                    $this->parametricSyncPhaseTesting($parametric, $companyTesting, $parametricService->getResponse());
                }
            }

        }
        else{

            \Log::debug("JOB PARAMETRICAS ------ Generales");

            $company = AccountPrepagoBags::where('fel_company_id', $data['company_id'])->first();

            $parametricService = new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());

            foreach ($data['data'] as $parametric){
                $parametricService->get($parametric, FelParametric::getUpdatedAt($parametric, $company->company_id), true);
                FelParametric::saveParametrics($parametric, $company->company_id, $parametricService->getResponse());
            }

        }
    }

    public function parametricSyncPhaseProduction($parametricUpdate, $company)
    {
        $parametricService = new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());
        
        foreach ($parametricUpdate as $parametric) {
            $parametricService->get($parametric, FelParametric::getUpdatedAt($parametric, $company->company_id), true);
            FelParametric::saveParametrics($parametric, $company->company_id, $parametricService->getResponse());
        }
    }

    public function parametricSyncPhaseTesting($type, $companies, $data)
    {
        foreach ($companies as $company) {
            FelParametric::saveParametrics($type, $company->company_id, $data);
        }
    }
}
