<?php

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use Carbon\Carbon;
class UpdatePrefacturaNumberCounter
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {

        \App\Models\Company::cursor()->each(function ($company) {

            $numer_counter = 1;
            \Log::debug("company : " . $company->id);
            FelInvoiceRequest::whereCompanyId($company->id)->whereNull('cuf')->orderBy('id')->withTrashed()->cursor()->each(function($invoice) use(&$numer_counter){

                $now = Carbon::now();
                DB::statement("update invoices set number=null, updated_at='$now' where id=$invoice->id_origin");
                DB::statement("update fel_invoice_requests set numeroFactura=0, prefactura_number=$numer_counter where id=" . $invoice->id);
                $numer_counter ++;


            });
            \Log::debug("update  number_prefactura_counter with ". $numer_counter . ' from company_id ' . $company->id);
            DB::statement("update fel_company set prefactura_number_counter=$numer_counter where company_id=$company->id ");


        });
    }
}
