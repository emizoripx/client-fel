<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use Illuminate\Console\Command;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
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
                $parametricService = new Parametric($company->fel_company_token->getAccessToken(), $company->fel_company_token->getHost());

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
}
