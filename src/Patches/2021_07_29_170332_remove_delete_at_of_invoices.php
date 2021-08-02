<?php

use App\Models\Invoice;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

class RemoveDeleteAtOfInvoices
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        // TODO se comento porque hubieron problemas en desarrollo

        // \DB::table('fel_invoice_requests')->whereNotNull('cuf')->whereNotNull('deleted_at')->update([
        //     'deleted_at' => null
        // ]);

        // \Log::debug("Updated FelInvoiceRequest");

        // \DB::table('invoices')->whereNotNull('deleted_at')->update([
        //     'deleted_at' => null
        // ]);

        // \Log::debug("Updated Invoices");

    }
}
