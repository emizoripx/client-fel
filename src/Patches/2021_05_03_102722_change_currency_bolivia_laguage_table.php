<?php

class ChangeCurrencyBoliviaLaguageTable
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->whereId(93)->update([
            "code" => "BOB",
            "decimal_separator" => ",",
            "exchange_rate" => 1,
            "name" => "Boliviano",
            "precision" => 2,
            "swap_currency_symbol" => false,
            "symbol" => "Bs",
            "thousand_separator" => "."
        ]);

        Artisan::call("emizor:warm-cache");
    }
}
