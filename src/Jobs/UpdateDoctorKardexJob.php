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

        $items = is_string($invoice->line_items) ? json_decode($invoice->line_items, true) : $invoice->line_items;
        if (!is_array($items)) return;

        $clientName = $invoice->client ? $invoice->client->name : 'Consumidor Final';
        $clientNit = $invoice->client ? $invoice->client->id_number : '';

        DB::transaction(function () use ($invoice, $items, $clientName, $clientNit) {
            foreach ($items as $item) {
                if (!is_array($item)) continue;
                
                $nitMedico = trim($item['nitDocumentoMedico'] ?? '');
                if (empty($nitMedico)) continue;

                $doctor = FelDoctor::where('company_id', $invoice->company_id)
                    ->where('nit_documento', $nitMedico)
                    ->first();

                if (!$doctor) continue;

                $quantity = (float) ($item['quantity'] ?? 1);
                $unitPrice = (float) ($item['cost'] ?? 0);
                $lineTotal = (float) ($item['line_total'] ?? ($quantity * $unitPrice));

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
                    'nro_quirofano' => $item['nroQuirofanoSalaOperaciones'] ?? null,
                    'nro_factura_medico' => $item['nroFacturaMedico'] ?? null,
                    'especialidad_medico' => $item['especialidadMedico'] ?? null,
                    'estado_factura' => $invoice->status_id == 2 ? 'Emitida' : 'Activa',
                ]);

                $summary = FelDoctorKardexSummary::firstOrCreate(
                    ['company_id' => $invoice->company_id, 'doctor_id' => $doctor->id]
                );

                $summary->total_procedimientos += 1;
                $summary->total_monto_generado += $lineTotal;
                $summary->promedio_por_intervencion = $summary->total_procedimientos > 0 
                    ? ($summary->total_monto_generado / $summary->total_procedimientos) 
                    : 0;

                $invoiceDate = Carbon::parse($invoice->date);
                if (!$summary->primer_servicio || $invoiceDate->lt($summary->primer_servicio)) {
                    $summary->primer_servicio = $invoiceDate;
                }
                if (!$summary->ultimo_servicio || $invoiceDate->gt($summary->ultimo_servicio)) {
                    $summary->ultimo_servicio = $invoiceDate;
                }

                $summary->save();
            }
        });
    }
}
