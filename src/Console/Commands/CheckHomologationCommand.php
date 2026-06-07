<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use Illuminate\Console\Command;
use EmizorIpx\ClientFel\Models\FelSyncProduct;

class CheckHomologationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fel:check-homologation {company_id? : El ID de la compañía para filtrar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa y lista los productos que tienen códigos de paramétricas nulos (pendientes de homologación)';

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
        $companyId = $this->argument('company_id');

        // 1. Productos desvinculados (con null) - Pendientes de Homologación
        $queryPending = FelSyncProduct::query()
            ->where(function ($q) {
                $q->whereNull('codigo_actividad_economica')
                  ->orWhereNull('codigo_producto_sin');
            });

        if ($companyId) {
            $queryPending->where('company_id', $companyId);
        }

        $pendingProducts = $queryPending->get();

        // 2. Homologaciones Huérfanas (Tienen un código, pero ya no existe en la tabla de paramétricas)
        $queryOrphanedActividades = \Illuminate\Support\Facades\DB::table('fel_sync_products as fsp')
            ->leftJoin('fel_activities as fa', function($join) {
                $join->on('fsp.codigo_actividad_economica', '=', 'fa.codigo')
                     ->on('fsp.company_id', '=', 'fa.company_id');
            })
            ->whereNotNull('fsp.codigo_actividad_economica')
            ->whereNull('fa.codigo')
            ->select('fsp.company_id', 'fsp.id_origin', 'fsp.codigo_actividad_economica as code', \Illuminate\Support\Facades\DB::raw("'Actividad Económica' as type"));

        $queryOrphanedSin = \Illuminate\Support\Facades\DB::table('fel_sync_products as fsp')
            ->leftJoin('fel_sin_products as fsin', function($join) {
                $join->on('fsp.codigo_producto_sin', '=', 'fsin.codigo')
                     ->on('fsp.company_id', '=', 'fsin.company_id');
            })
            ->whereNotNull('fsp.codigo_producto_sin')
            ->whereNull('fsin.codigo')
            ->select('fsp.company_id', 'fsp.id_origin', 'fsp.codigo_producto_sin as code', \Illuminate\Support\Facades\DB::raw("'Producto SIN' as type"));

        if ($companyId) {
            $queryOrphanedActividades->where('fsp.company_id', $companyId);
            $queryOrphanedSin->where('fsp.company_id', $companyId);
        }

        $orphanedProducts = $queryOrphanedActividades->get()->merge($queryOrphanedSin->get());

        // Reporte
        if ($pendingProducts->isEmpty() && $orphanedProducts->isEmpty()) {
            $this->info("Todo está en orden. No hay homologaciones huérfanas ni productos pendientes.");
            return 0;
        }

        if ($pendingProducts->isNotEmpty()) {
            $this->warn("Se encontraron " . $pendingProducts->count() . " productos pendientes de homologación (valores en null).");
            $dataPending = $pendingProducts->map(function ($p) {
                return [
                    $p->company_id,
                    $p->id_origin,
                    is_null($p->codigo_actividad_economica) ? 'Falta Actividad' : 'OK',
                    is_null($p->codigo_producto_sin) ? 'Falta Producto SIN' : 'OK',
                ];
            });
            $this->table(['Company ID', 'Product ID', 'Actividad Económica', 'Producto SIN'], $dataPending);
        }

        if ($orphanedProducts->isNotEmpty()) {
            $this->error("Se encontraron " . $orphanedProducts->count() . " HOMOLOGACIONES HUÉRFANAS (Tienen código asignado pero ya no existe en las paramétricas).");
            $dataOrphaned = $orphanedProducts->map(function ($p) {
                return [
                    $p->company_id,
                    $p->id_origin,
                    $p->type,
                    $p->code
                ];
            });
            $this->table(['Company ID', 'Product ID', 'Tipo de Paramétrica', 'Código Inválido'], $dataOrphaned);
        }

        return 0;
    }
}
