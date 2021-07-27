<?php

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;

class UpdateCounterPostpagoToCompaniesPostpago
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        $felCompanies = AccountPrepagoBags::where('is_postpago', true)->get();

        foreach ($felCompanies as $felCompany) {
            $numberInvoices = FelInvoiceRequest::select('type_document_sector_id', \DB::raw('COUNT(*) as counter'))->where('company_id', $felCompany->company_id)->where('codigoEstado', 690)->groupBy('type_document_sector_id')->get();

            \Log::debug("Company");
            \Log::debug(json_encode($felCompany->company_id));
            \Log::debug("Number Invoice");
            \Log::debug(json_encode($numberInvoices));

            if($numberInvoices) {
                $numberInvoices->map( function ($item) use ($felCompany){
                    FelCompanyDocumentSector::where('company_id', $felCompany->company_id)->where('fel_doc_sector_id', $item->type_document_sector_id)->update([
                        'postpago_counter' => $item->counter
                    ]);
                });
            }
            \Log::debug("Fin Update");

        }
    }

}
