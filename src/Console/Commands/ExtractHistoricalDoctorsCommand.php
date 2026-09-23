<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use Illuminate\Console\Command;
use EmizorIpx\ClientFel\Models\FelDoctor;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class ExtractHistoricalDoctorsCommand extends Command
{
    protected $signature = 'emizor:extract-doctors 
                            {--company_id= : ID de la empresa para extraer medicos} 
                            {--dry-run : Ejecutar sin persistir cambios en la base de datos}';

    protected $description = 'Extrae y registra medicos unicos a partir de las facturas historicas del Sector 17 (Salud)';

    public function handle()
    {
        $companyId = $this->option('company_id');
        $isDryRun = $this->option('dry-run');

        $this->info("=================================================");
        $this->info(" EXTRACCIÓN DE MÉDICOS HISTÓRICOS (SECTOR 17) ");
        $this->info("=================================================");

        if ($isDryRun) {
            $this->warn("MODO DRY-RUN ACTIVADO: No se guardarán cambios en la base de datos.");
        }

        $query = DB::table('invoices')->whereNotNull('line_items');
        if ($companyId) {
            $query->where('company_id', $companyId);
            $this->info("Filtrando por Company ID: {$companyId}");
        } else {
            $this->info("Procesando todas las empresas con facturas disponibles...");
        }

        $totalInvoices = (clone $query)->count();
        $this->info("Total de facturas a inspeccionar: {$totalInvoices}");

        if ($totalInvoices === 0) {
            $this->info("No se encontraron facturas con line_items para procesar.");
            return 0;
        }

        $detectedDoctors = []; // key: companyId_nit => data
        $bar = $this->output->createProgressBar($totalInvoices);
        $bar->start();

        $query->orderBy('id', 'asc')->chunk(200, function ($invoices) use (&$detectedDoctors, $bar) {
            foreach ($invoices as $invoice) {
                $bar->advance();

                $rawItems = $invoice->line_items;
                if (empty($rawItems)) continue;

                $items = is_string($rawItems) ? json_decode($rawItems, true) : $rawItems;
                if (!is_array($items)) continue;

                foreach ($items as $item) {
                    if (!is_array($item)) continue;

                    $nit = trim($item['nitDocumentoMedico'] ?? $item['nit_documento_medico'] ?? $item['nitMedico'] ?? '');
                    $nombre = trim($item['nombreApellidoMedico'] ?? $item['nombre_apellido_medico'] ?? $item['nombreMedico'] ?? '');
                    $matricula = trim($item['nroMatriculaMedico'] ?? $item['nro_matricula_medico'] ?? $item['matriculaMedico'] ?? '');
                    $especialidad = trim($item['especialidadMedico'] ?? $item['especialidad_medico'] ?? $item['especialidad'] ?? '');

                    // Se requiere al menos un nombre o NIT para identificar al médico
                    if (empty($nombre) && empty($nit)) {
                        continue;
                    }

                    // Si no tiene NIT pero tiene nombre, usar un identificador seguro
                    $docNit = !empty($nit) ? $nit : 'SN-' . substr(md5(strtolower($nombre)), 0, 8);
                    $docKey = "{$invoice->company_id}_{$docNit}";

                    if (!isset($detectedDoctors[$docKey])) {
                        $detectedDoctors[$docKey] = [
                            'company_id' => $invoice->company_id,
                            'nombre_apellido' => $nombre ?: 'Médico sin nombre',
                            'nit_documento' => $docNit,
                            'nro_matricula' => $matricula ?: null,
                            'especialidad' => $especialidad ?: 'Medicina General',
                            'activo' => true,
                            'facturas_count' => 1,
                        ];
                    } else {
                        $detectedDoctors[$docKey]['facturas_count']++;
                        // Completar datos faltantes si la factura previa no los tenía
                        if (empty($detectedDoctors[$docKey]['nro_matricula']) && !empty($matricula)) {
                            $detectedDoctors[$docKey]['nro_matricula'] = $matricula;
                        }
                        if (($detectedDoctors[$docKey]['especialidad'] === 'Medicina General') && !empty($especialidad)) {
                            $detectedDoctors[$docKey]['especialidad'] = $especialidad;
                        }
                        if ($detectedDoctors[$docKey]['nombre_apellido'] === 'Médico sin nombre' && !empty($nombre)) {
                            $detectedDoctors[$docKey]['nombre_apellido'] = $nombre;
                        }
                    }
                }
            }
        });

        $bar->finish();
        $this->newLine(2);

        $totalDetected = count($detectedDoctors);
        $this->info("Médicos únicos detectados: {$totalDetected}");

        if ($totalDetected === 0) {
            $this->info("No se encontraron registros de médicos en las facturas analizadas.");
            return 0;
        }

        // Mostrar resumen de muestra
        $tableRows = [];
        $sample = array_slice($detectedDoctors, 0, 15);
        foreach ($sample as $d) {
            $tableRows[] = [
                $d['company_id'],
                $d['nombre_apellido'],
                $d['nit_documento'],
                $d['nro_matricula'] ?? '-',
                $d['especialidad'],
                $d['facturas_count'],
            ];
        }
        $this->table(['Company', 'Nombre y Apellido', 'NIT / Doc', 'Matrícula', 'Especialidad', 'Servicios'], $tableRows);

        if ($totalDetected > 15) {
            $this->comment("... y " . ($totalDetected - 15) . " médicos adicionales.");
        }

        if ($isDryRun) {
            $this->info("Simulación finalizada. No se alteró la base de datos.");
            return 0;
        }

        $this->info("Guardando médicos en la tabla 'fel_doctors'...");
        $createdCount = 0;
        $existingCount = 0;

        foreach ($detectedDoctors as $d) {
            $existing = FelDoctor::where('company_id', $d['company_id'])
                ->where('nit_documento', $d['nit_documento'])
                ->first();

            if (!$existing) {
                FelDoctor::create([
                    'company_id' => $d['company_id'],
                    'nombre_apellido' => $d['nombre_apellido'],
                    'nit_documento' => $d['nit_documento'],
                    'nro_matricula' => $d['nro_matricula'],
                    'especialidad' => $d['especialidad'],
                    'activo' => true,
                ]);
                $createdCount++;
            } else {
                $existingCount++;
            }
        }

        $this->info("Proceso completado con éxito:");
        $this->info("- Nuevos médicos registrados: {$createdCount}");
        $this->info("- Médicos ya existentes: {$existingCount}");

        return 0;
    }
}
