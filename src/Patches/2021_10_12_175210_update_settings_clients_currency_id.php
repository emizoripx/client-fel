<?php


class UpdateSettingsClientsCurrencyId
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Client::cursor()->each(function ($client) {
            
            $init = microtime(true);
            $settings = $client->settings;
            $update_setting = new \stdClass; 
            foreach ($settings as $key => $val) {
                $update_setting->{$key} = $val;
                if (property_exists($settings, 'currency_id')) {
                    $settings->currency_id = "93";// default BOLIVIANO
                }
            }

            $client->settings = $update_setting;
            $client->save();
            
            \Log::debug("=====================COMPLETADO EN : " . (microtime(true) - $init) . " ===================");

        });
    }
}
