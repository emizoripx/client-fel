<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeDataCodigoColumnInParametrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_activities', function (Blueprint $table) {
            $table->string('codigo', 20)->change();
        });


        Schema::table('fel_captions', function (Blueprint $table) {
            $table->string('codigo', 20)->change();
        });
        
        Schema::table('fel_countries', function (Blueprint $table) {
            $table->string('codigo', 20)->change();
        });
        
        Schema::table('fel_currencies', function (Blueprint $table) {
            $table->string('codigo', 20)->change();
        });
        
        Schema::table('fel_identity_document_types', function (Blueprint $table) {
            $table->string('codigo', 20)->change();
        });
        
        Schema::table('fel_payment_methods', function (Blueprint $table) {
            $table->string('codigo', 20)->change();
        });

        Schema::table('fel_revocation_reasons', function (Blueprint $table) {
            $table->string('codigo', 20)->change();
        });

        Schema::table('fel_sin_products', function (Blueprint $table) {
            $table->string('codigo', 20)->change();
        });
        
        Schema::table('fel_units', function (Blueprint $table) {
            $table->string('codigo', 20)->change();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parametrics', function (Blueprint $table) {
            //
        });
    }
}
