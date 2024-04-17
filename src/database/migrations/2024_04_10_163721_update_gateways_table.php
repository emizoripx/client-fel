<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!\DB::table("gateways")->whereId(1001)->exists()){
            \DB::table("gateways")->insert([
                "id" => 1001,
                "name" => "Vendis Cobros QR",
                "key" => "d14dd26a47cec830x11x5700bfb67ccc",
                "provider" => "CobrosQr",
                "visible" => 1,
                "sort_order" => 10000,
                "site_url" => "https://vendis.com.bo",
                "is_offsite" => 0,
                "is_secure" => 1,
                "fields" => '"{ \"endpoint\":\"\",\"email\":\"\",\"password\":\"\",\"access_token\":\"\",\"device_id\":\"\",\"expires_at\":\"\"}"',
                "default_gateway_type_id" => 1001
            ]);
        }
        DB::table("gateways")->where("id",">", 0)->update(["visible"=> 0]);
        DB::table("gateways")->whereIn("id", [20, 1000,1001])->update(["visible"=> 1]); // enable only stripe, qr1, qr2

        Artisan::call("emizor:warm-cache");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
};
