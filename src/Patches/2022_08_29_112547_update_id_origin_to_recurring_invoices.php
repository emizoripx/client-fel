<?php

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

class UpdateIdOriginToRecurringInvoices
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        // TODO
        $recurring_invoices = \DB::table('recurring_invoices')->pluck('id');

        \Log::debug("IDs Facturas recurrentes a actualizar: " . json_encode($recurring_invoices));

        $fel_invoices = FelInvoiceRequest::whereIn('id_origin', $recurring_invoices)->whereNull('deleted_at')->get();

        foreach ($fel_invoices as $fel_invoice) {
            
            $fel_invoice->recurring_id_origin = $fel_invoice->id_origin;
            $fel_invoice->save();

            \Log::debug("Actualizado Recurrente ID: " . $fel_invoice->id_origin);

        }

    }
}
