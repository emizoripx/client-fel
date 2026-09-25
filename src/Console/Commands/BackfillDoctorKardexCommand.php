<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use EmizorIpx\ClientFel\Jobs\UpdateDoctorKardexJob;
use Illuminate\Support\Facades\DB;

class BackfillDoctorKardexCommand extends Command
{
    protected $signature = 'emizor:backfill-doctor-kardex 
                            {--company_id= : ID de la empresa para extraer medicos}';

    protected $description = 'Puebla el historial del Kardex (movimientos y totales) a partir de facturas pasadas del Sector 17';

    public function handle()
    {
        // Evitar agotamiento de memoria
        ini_set('memory_limit', '2G');
        DB::disableQueryLog();

        $companyId = $this->option('company_id');

        $this->info("=================================================");
        $this->info("   BACKFILL DE KARDEX DE MÉDICOS (SECTOR 17)     ");
        $this->info("=================================================");

        $query = Invoice::whereNotNull('line_items');
        
        if ($companyId) {
            $query->where('company_id', $companyId);
            $this->info("Filtrando por Company ID: {$companyId}");
        }

        $totalInvoices = (clone $query)->count();
        $this->info("Total de facturas a procesar: {$totalInvoices}");

        if ($totalInvoices === 0) {
            $this->info("No se encontraron facturas.");
            return 0;
        }

        $bar = $this->output->createProgressBar($totalInvoices);
        $bar->start();

        // Procesar en lotes (chunks) para no saturar memoria
        $query->orderBy('id', 'asc')->chunkById(200, function ($invoices) use ($bar) {
            foreach ($invoices as $invoice) {
                // Reutilizamos toda la lógica que ya creamos en el Job
                UpdateDoctorKardexJob::dispatchSync($invoice->id);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        
        $this->info("¡Backfill completado exitosamente!");
        $this->info("Los resúmenes y movimientos en el Kardex están ahora actualizados al día de hoy.");

        return 0;
    }
}
