<?php

use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;

class UpdateFelCompanyDocumentSectors
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        $felCompanies = DB::table('fel_company')
                        ->join('fel_sector_document_types', 'fel_company.company_id', 'fel_sector_document_types.company_id')
                        ->select('fel_company.id as id', 'fel_company.company_id as company_id', 'fel_sector_document_types.codigo as document_sector_code')
                        ->get();

        foreach ($felCompanies as $company) {
            
            if(!$this->checkExists($company)){
                FelCompanyDocumentSector::create([
                    'company_id' => $company->company_id,
                    'fel_doc_sector_id' => $company->document_sector_code,
                    'fel_company_id' => $company->id
                ]);
            }

        }
    }

    public function checkExists($company){

        return FelCompanyDocumentSector::where('fel_company_id', $company->id)->where('fel_doc_sector_id', $company->document_sector_code)->exists();

    }
}
