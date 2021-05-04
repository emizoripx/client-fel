<?php

class ChangeLanguageBoliviaContryTable
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->whereId(68)->update([
            "capital" => "Sucre (BO1)",
            "citizenship" => "Boliviano",
            "country_code" => "068",
            "currency" => "boliviano",
            "currency_code" => "BOB",
            "currency_sub_unit" => "centavo",
            "decimal_separator" => ",",
            "eea" => false,
            "full_name" => "Estado Plurinacional de Bolivia",
            "id" => "68",
            "iso_3166_2" => "BO",
            "iso_3166_3" => "BOL",
            "name" => "Estado Plurinacional de Bolivia",
            "region_code" => "019",
            "sub_region_code" => "005",
            "swap_currency_symbol" => false,
            "swap_postal_code" => false,
            "thousand_separator" => "."
        ]);

        Artisan::call("emizor:warm-cache");
    }
}
