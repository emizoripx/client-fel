<?php

class UpdateCompanyPdfVariables
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        $variables = [
                '$company.name',
                '$fel.casa_matriz',
                '$company.website',
                '$company.email',
                '$company.phone',
            ];


        $companies = \App\Models\Company::all();

        foreach ($companies as $company) {
            $settings = $company->settings;
            $settings->pdf_variables->company_details = $variables;
            $company->settings = $settings;
            $company->save();
        }
    }
}
