<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelUfv;

trait RecurringInvoiceServiceTrait {

    public function recalculateTotals() {

        $company = $this->recurring_entity->company;

        if( $company->settings->id_number == '347399028' && $this->recurring_entity->fel_invoice->type_document_sector_id = TypeDocumentSector::ALQUILER_BIENES_INMUEBLES ) {

            $last_day_month = Carbon::now()->endOfMonth()->format('Y-m-d');

            \Log::debug("End Day of Month: " . $last_day_month);


            $response = FelUfv::where('fecha', $last_day_month)->first();

            if( empty($response) && !$response ) {
                
                throw new ClientFelException('No existe un Registro de Valor UFV para ' . $last_day_month);

            }

            $ufv_value = $response->val_ufv;

            \Log::debug("Ufv current value: " . $ufv_value);

            $total_amout = 0;
            $details = [];

            foreach ($this->recurring_entity->line_items as $detail) {
                
                \Log::debug("Detail Recurring Invoice: " . json_encode($detail));

                $cost = round(($detail->montoUFV * $ufv_value), 2);
                
                $detail->cost = $cost;
                $detail->line_total = $detail->quantity * $cost;
                $detail->gross_line_total = $detail->quantity * $cost;

                $total_amout += ($detail->quantity * $cost);

                $details[] = $detail;

            }

            \Log::debug("Details updated: " . json_encode($details));
            \Log::debug("Monto Total: " . $total_amout);

            $this->recurring_entity->line_items = $details;
            $this->recurring_entity->amount = $total_amout;
            $this->recurring_entity->balance = $total_amout;
            $this->recurring_entity->saveQuietly();

            $extras = $this->recurring_entity->fel_invoice->getExtras();

            $extras->valorUFV = (string) $ufv_value;

            $this->recurring_entity->fel_invoice->extras = json_encode($extras);
            $this->recurring_entity->fel_invoice->detalles = $details;
            $this->recurring_entity->fel_invoice->montoTotal = $total_amout;
            $this->recurring_entity->fel_invoice->montoTotalMoneda = $total_amout;
            $this->recurring_entity->fel_invoice->montoTotalSujetoIva = $total_amout;
            $this->recurring_entity->fel_invoice->save();

        }

    }

}