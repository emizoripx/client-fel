<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PartnerConfiguration;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;

class SyncParametricsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emizor:sync-parametrics {--company= : ID de la empresa local} {--full : Forzar sincronización completa (vaciar obsoletos)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza las paramétricas del SIN para las empresas en Producción. Usa --full para limpiar paramétricas obsoletas.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $companyId = $this->option('company');
        $isFullSync = $this->option('full');

        $query = AccountPrepagoBags::where('phase', 'Production');
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $companies = $query->get();

        if ($companies->isEmpty()) {
            $this->error('No se encontraron empresas en Producción para sincronizar.');
            return;
        }

        $this->info("Iniciando sincronización para " . $companies->count() . " empresas.");
        $this->info("Modo de Sincronización: " . ($isFullSync ? 'COMPLETA (Full Sync)' : 'PARCIAL (Delta Sync)'));

        $parametrics = TypeParametrics::getAll();

        foreach ($companies as $company) {
            $this->line("====================================================");
            $this->info("Sincronizando Empresa #{$company->company_id}");
            
            try {
                $parametricService = $this->getParametricService($company);

                foreach ($parametrics as $parametric) {
                    $this->line("  -> Sincronizando: {$parametric} ...");
                    
                    // Si es Full Sync, NO enviamos el updated_at
                    $updatedAt = $isFullSync ? '' : FelParametric::getUpdatedAt($parametric, $company->company_id);
                    $all = $isFullSync ? 'true' : '';

                    $parametricService->get($parametric, $updatedAt, $all);
                    $response = $parametricService->getResponse();
                    
                    if (is_array($response)) {
                        FelParametric::saveParametrics($parametric, $company->company_id, $response, $isFullSync);
                        $this->info("     Guardado exitosamente (" . count($response) . " registros recibidos).");
                    } else {
                        $this->error("     Respuesta vacía o inválida obtenida del API.");
                    }
                }
            } catch (\Exception $ex) {
                $this->error("Error sincronizando la empresa #{$company->company_id}: " . $ex->getMessage());
            }
        }
        
        $this->info("Sincronización finalizada.");
    }

    /**
     * Resolver servicio Parametric soportando arquitectura Partner y Directa
     *
     * @param AccountPrepagoBags $company
     * @return Parametric
     * @throws \Exception
     */
    protected function getParametricService($company)
    {
        $companyModel = Company::find($company->company_id);
        $isPartner = $companyModel && !empty($companyModel->settings->is_b2b2b_partner);

        if ($isPartner) {
            $phase = ($company->phase === 'Production') ? 'production' : 'testing';
            $config = PartnerConfiguration::getConfig($phase);
            $host = rtrim($config['api_url'] ?? '', '/');
            $token = $config['partner_token'] ?? '';
            $tenantKey = $companyModel->settings->tenant_id ?? ($companyModel->settings->id_number ?? $company->company_id);

            if (empty($token)) {
                throw new \Exception("Partner Token para fase '$phase' no está configurado.");
            }

            return new Parametric($token, $host, $tenantKey);
        }

        if (!$company->fel_company_token) {
            throw new \Exception("La empresa #{$company->company_id} no cuenta con credenciales FEL locales (fel_company_token).");
        }

        return new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());
    }
}
