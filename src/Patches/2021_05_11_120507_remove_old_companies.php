<?php

use App\Models\Company as ModelsCompany;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Services\Company\Company;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Services\PurgeCompanyDataService;

class RemoveOldCompanies
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {

        $felCompanyTokens = FelClientToken::all();

        foreach ($felCompanyTokens as $company) {

            try {
                $companyService = new Company($company->access_token, $company->host);

                $response = $companyService->getCompany();
       
                if (is_null($response)) {
                    throw new Exception('');
                }

            } catch (Exception $ex) {
                $felCompany = AccountPrepagoBags::where('company_id', $company->account_id)->first();
                if( !is_null($felCompany) && $felCompany->phase == 'Testing'){
                    \Log::debug("Deleting Company....". $company->account_id);
                    $this->removeCompany($company->account_id);
                    $company->delete();
                    $felCompany->delete();
                }
                
            }


        }
        
    }

    public function removeCompany($company_id){

        try {
            $purgeService = new PurgeCompanyDataService();

            $purgeService->setCompanyId($company_id);

            $purgeService
            ->purgeInvoices()
            ->purgeSyncProducts()
            ->purgeSinProducts()
            ->purgeSectorDocuments()
            ->purgeActivities()
            ->purgeCaptions()
            ->purgeClients()
            ->purgeBranches()
            ->purgePOS()
            ->purgeCompanyDocumentSector();

            $companyModel = ModelsCompany::where('id', $company_id)->first();

            \Log::debug("Company Model...");
            \Log::debug(json_encode($companyModel));

            if(!is_null($companyModel)){
                $companyModel->invoices()->forceDelete();
                $companyModel->clients()->forceDelete();
                $companyModel->products()->forceDelete();
                $companyModel->save();

                $companyModel->delete();
            }

        } catch (Exception $ex) {
            \Log::debug("Error al eliminar datos de la Compañia ". $ex->getMessage());
        }

    }
}
