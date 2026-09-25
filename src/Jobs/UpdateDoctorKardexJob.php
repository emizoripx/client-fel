<?php

namespace EmizorIpx\ClientFel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;
use EmizorIpx\ClientFel\Models\FelDoctor;
use EmizorIpx\ClientFel\Models\FelDoctorKardexSummary;
use EmizorIpx\ClientFel\Models\FelDoctorKardexMovement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateDoctorKardexJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoiceId;

    public function __construct($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function handle()
    {
        $invoice = Invoice::find($this->invoiceId);
        if (!$invoice || !$invoice->line_items) return;

        $felInvoice = DB::table('fel_invoice_requests')->where('id_origin', $invoice->id)->first();
        if (!$felInvoice) return;

        // Si la factura nunca fue válida fiscalmente, la ignoramos completamente del Kardex
        if (in_array($felInvoice->estado, ['RECHAZADA', 'INVALIDA']) || in_array($felInvoice->codigoEstado, [902, 904])) {
            return;
        }

        $items = is_string($invoice->line_items) ? json_decode($invoice->line_items, true) : json_decode(json_encode($invoice->line_items), true);
        if (!is_array($items)) return;

        $clientName = $invoice->client ? $invoice->client->name : 'Consumidor Final';
        $clientNit = $invoice->client ? $invoice->client->id_number : '';
        $isAnulada = ($felInvoice->estado === 'ANULADA' || $felInvoice->codigoEstado == 905);

        DB::transaction(function () use ($invoice, $felInvoice, $items, $clientName, $clientNit, $isAnulada) {
            
            // 1. Limpiar movimientos previos de esta factura (Idempotencia)
            FelDoctorKardexMovement::where('invoice_id', $invoice->id)->delete();
            
            $doctorsToUpdate = [];

            foreach ($items as $item) {
                if (!is_array($item)) continue;
                
                $nitMedico = trim($item['nitDocumentoMedico'] ?? $item['nit_documento_medico'] ?? $item['nitMedico'] ?? '');
                $nombreMedico = trim($item['nombreApellidoMedico'] ?? $item['nombre_apellido_medico'] ?? $item['nombreMedico'] ?? '');
                
                // Si no hay NIT, intentar usar el key sintético generado en extract-doctors
                if (empty($nitMedico) && !empty($nombreMedico)) {
                    $nitMedico = 'SN-' . substr(md5(strtolower($nombreMedico)), 0, 8);
                }

                if (empty($nitMedico)) continue;

                $doctor = FelDoctor::where('company_id', $invoice->company_id)
                    ->where('nit_documento', $nitMedico)
                    ->first();

                if (!$doctor) continue;

                $doctorsToUpdate[$doctor->id] = $doctor;

                $quantity = (float) ($item['quantity'] ?? 1);
                $unitPrice = (float) ($item['cost'] ?? 0);
                $lineTotal = (float) ($item['line_total'] ?? ($quantity * $unitPrice));

                // 2. Crear el Movimiento
                FelDoctorKardexMovement::create([
                    'company_id' => $invoice->company_id,
                    'doctor_id' => $doctor->id,
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->number,
                    'invoice_date' => $invoice->date,
                    'client_name' => $clientName,
                    'client_nit' => $clientNit,
                    'procedimiento' => $item['notes'] ?? 'Servicio Médico',
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'nro_quirofano' => $item['nroQuirofanoSalaOperaciones'] ?? $item['nro_quirofano_sala_operaciones'] ?? null,
                    'nro_factura_medico' => $item['nroFacturaMedico'] ?? $item['nro_factura_medico'] ?? null,
                    'especialidad_medico' => $item['especialidadMedico'] ?? $item['especialidad_medico'] ?? $item['especialidad'] ?? null,
                    'estado_factura' => $isAnulada ? 'ANULADA' : ($felInvoice->estado ?: 'VALIDA'),
                ]);
            }

            // 3. Recalcular el Summary (Cube) para los doctores afectados de forma real
            foreach ($doctorsToUpdate as $doctor) {
                $summary = FelDoctorKardexSummary::firstOrCreate(
                    ['company_id' => $invoice->company_id, 'doctor_id' => $doctor->id]
                );

                // Solo sumarizamos las NO ANULADAS
                $resumen = FelDoctorKardexMovement::where('company_id', $invoice->company_id)
                    ->where('doctor_id', $doctor->id)
                    ->where('estado_factura', '!=', 'ANULADA')
                    ->selectRaw('COUNT(*) as total_proc, SUM(line_total) as total_monto, MIN(invoice_date) as primer, MAX(invoice_date) as ultimo')
                    ->first();

                $summary->total_procedimientos = $resumen->total_proc ?? 0;
                $summary->total_monto_generado = $resumen->total_monto ?? 0;
                $summary->promedio_por_intervencion = $summary->total_procedimientos > 0 
                    ? ($summary->total_monto_generado / $summary->total_procedimientos) 
                    : 0;

                $summary->primer_servicio = $resumen->primer ?: null;
                $summary->ultimo_servicio = $resumen->ultimo ?: null;

                $summary->save();
            }
        });
    }
}
