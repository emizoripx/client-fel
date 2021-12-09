<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsPrefacturaNumberCounterToFelCompanyTable extends Migration
{
    public function up()
    {

        Schema::table("fel_company", function (Blueprint $table) {
            $table->unsignedInteger("prefactura_number_counter")->default(0);
        });
    }


    public function down()
    {
        Schema::table("fel_company", function (Blueprint $table) {
            $table->dropColumn("prefactura_number_counter");
        });
    }
}
