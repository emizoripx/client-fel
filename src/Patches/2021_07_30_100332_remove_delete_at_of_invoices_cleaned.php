<?php

class RemoveDeleteAtOfInvoicesCleaned
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {

        $fel_invoice_request_ids = \DB::table('fel_invoice_requests')->whereNotNull('cuf')->whereNotNull('deleted_at')->pluck('id_origin');
        $ids_to_array = [];

        if (!empty($fel_invoice_request_ids)){

            $ids_to_array = collect($fel_invoice_request_ids)->toArray();
            
            $ids = "(" . implode(",", $ids_to_array) . ")";
            
            \DB::table('fel_invoice_requests')->whereNotNull('cuf')->whereNotNull('deleted_at')->update([
                'deleted_at' => null
            ]);
            
            \Log::debug("Updated FelInvoiceRequest");
            
            \DB::statement("update invoices set number = SUBSTRING(number, 1, 4),deleted_at = null where deleted_at is not null and id in " . $ids);
            
            \Log::debug("Updated Invoices");
        } else {
            \Log::debug("No hay cambios que hacer");
        }
    }
}
