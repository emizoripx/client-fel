<?php

use App\Models\CompanyUser;

class RemoveDuedateInSettingOfCompanyUser
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        $companyUsers = CompanyUser::all();


            foreach ($companyUsers as $company) {
            
                try {
                    if ($company->settings) {
                        $index = array_search('due_date', $company->settings->table_columns->invoice);
        
                        if ($index) {
                            $invoiceSettings = $company->settings->table_columns->invoice;
                            $settings = $company->settings;
                            unset($invoiceSettings[$index]);
                            $invoice = [];
                            foreach ($invoiceSettings as $key => $value) {
                                $invoice[] = $value;
                            }

                            $settings->table_columns->invoice = $invoice;
                            $company->settings = $settings;
                            $company->save();
                            
                        }
                        
                    }
                    
                } catch (Exception $ex) {
                    \Log::debug("Error en company #". $company->company_id . ': '. $ex->getMessage());
                }
    
            }
        
    }
}
