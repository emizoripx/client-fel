<?php

use Illuminate\Database\Migrations\Migration;

use App\Models\Currency;
use Illuminate\Support\Facades\Artisan;
class CreateNewCurrencyFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $currency = Currency::find(1000);

        if (empty($currency) ){
            //insert new format for boliviano
            Currency::insert( ['id' => 1000, 'name' => 'Boliviano f2', 'code' => 'BOB', 'symbol' => 'Bs', 'precision' => '2', 'thousand_separator' => ',', 'decimal_separator' => '.']);
            Artisan::call('emizor:warm-cache'); 
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    
    }
}
