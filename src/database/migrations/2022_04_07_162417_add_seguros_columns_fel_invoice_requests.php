<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSegurosColumnsFelInvoiceRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("fel_invoice_requests", function(Blueprint $table){
            $table->decimal('ajusteAfectacionIva',17,2)->nullable();
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
            $table->dropColumn('ajusteAfectacionIva');
        });
   
    }
}
