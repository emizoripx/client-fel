<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlanillaCounterColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("fel_company", function(Blueprint $table){
            $table->unsignedInteger('planilla_number_counter')->default(0);
        });

        // add document number 
        Schema::table("fel_invoice_requests", function (Blueprint $table) {
            $table->unsignedInteger('document_number')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("fel_company", function (Blueprint $table) {
            $table->dropColumn('planilla_number_counter');
        });
        // add document number 
        Schema::table("fel_invoice_requests", function (Blueprint $table) {
            $table->dropColumn('document_number');
        });
    }
}
