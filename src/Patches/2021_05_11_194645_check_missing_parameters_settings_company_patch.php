<?php

use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
class CheckMissingParametersSettingsCompanyPatch
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        $companies = AccountPrepagoBags::whereNotNull('settings')->get();

        foreach ($companies as $company) {

            $settings = json_decode($company->settings);

            if (!isset($settings->caption_id) || !isset($settings->activity_id))
                $company->settings = null;

            if (!isset($settings->payment_method_id)) 
                $settings->payment_method_id = "1"; // EFECTIVO

            if (!isset($settings->currency_id)) 
                $settings->currency_id = "1"; // BOLIVIANOS

            $company->settings = json_encode($settings);
            $company->save();
        }
    }
}
