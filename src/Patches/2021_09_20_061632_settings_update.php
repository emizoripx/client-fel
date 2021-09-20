<?php

use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;

class SettingsUpdate
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        $array_setting=[];
        AccountPrepagoBags::cursor()->each( function($fel_company) {
            
            if (!empty($fel_company->settings)) {
                $setting = json_decode($fel_company->settings);

                if (!is_array($setting)){
                    $data = new \stdClass;
                    $data->codigo= 1;
                    $data->origen= null;
                    $data->incoterm= null;
                    $data->nro_lote= null;
                    $data->caption_id= $setting->caption_id;
                    $data->activity_id= $setting->activity_id;
                    $data->currency_id= $setting->currency_id;
                    $data->payment_method_id= $setting->payment_method_id;
                    $data->concentrado= null;
                    $data->tipo_cambio= null;
                    $data->hidden_fields= null;
                    $data->default_labels= null;
                    $data->puerto_transito= null;
                    $array_setting []=$data; 
                    $fel_company->settings = json_encode($array_setting);
                    $fel_company->save();
                    \Log::debug(["patch ", $fel_company->settings]);
                    \Log::debug(["patched ", $data]);
                }
            }
            
        });

    }
}
