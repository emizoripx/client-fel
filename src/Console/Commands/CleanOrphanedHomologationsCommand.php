<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use Illuminate\Console\Command;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Illuminate\Support\Facades\DB;

class CleanOrphanedHomologationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fel:clean-orphaned-homologations {company_id? : El ID de la compañía para filtrar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia (establece en null) las homologaciones huérfanas en fel_sync_products cuyos códigos ya no existen en las paramétricas';

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

        $this->info("Iniciando parcheo de homologaciones huérfanas...");

        // 1. Limpiar Actividades Económicas Huérfanas
        $queryActividades = DB::table('fel_sync_products as fsp')
            ->leftJoin('fel_activities as fa', function($join) {
                $join->on('fsp.codigo_actividad_economica', '=', 'fa.codigo')
                     ->on('fsp.company_id', '=', 'fa.company_id');
            })
            ->whereNotNull('fsp.codigo_actividad_economica')
            ->whereNull('fa.codigo');

        if ($companyId) {
            $queryActividades->where('fsp.company_id', $companyId);
        }

        $actividadesHuerfanasIds = $queryActividades->pluck('fsp.id')->toArray();
        $cleanedActividades = 0;

        if (!empty($actividadesHuerfanasIds)) {
            $cleanedActividades = FelSyncProduct::whereIn('id', $actividadesHuerfanasIds)
                ->update(['codigo_actividad_economica' => null]);
            $this->info("Limpiadas $cleanedActividades Actividades Económicas huérfanas.");
        }

        // 2. Limpiar Productos SIN Huérfanos
        $querySin = DB::table('fel_sync_products as fsp')
            ->leftJoin('fel_sin_products as fsin', function($join) {
                $join->on('fsp.codigo_producto_sin', '=', 'fsin.codigo')
                     ->on('fsp.company_id', '=', 'fsin.company_id');
            })
            ->whereNotNull('fsp.codigo_producto_sin')
            ->whereNull('fsin.codigo');

        if ($companyId) {
            $querySin->where('fsp.company_id', $companyId);
        }

        $sinHuerfanosIds = $querySin->pluck('fsp.id')->toArray();
        $cleanedSin = 0;

        if (!empty($sinHuerfanosIds)) {
            $cleanedSin = FelSyncProduct::whereIn('id', $sinHuerfanosIds)
                ->update(['codigo_producto_sin' => null]);
            $this->info("Limpiados $cleanedSin Productos SIN huérfanos.");
        }

        if ($cleanedActividades == 0 && $cleanedSin == 0) {
            $this->info("No se encontraron homologaciones huérfanas para limpiar.");
        } else {
            $this->info("Parcheo completado exitosamente.");
        }

        return 0;
    }
}
