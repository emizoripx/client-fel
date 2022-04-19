<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotaconciliacionColumnsFelInvoiceRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("fel_invoice_requests", function (Blueprint $table) {
            $table->decimal('debitoFiscalIva', 17, 2)->default(0);
            $table->decimal('creditoFiscalIva', 17, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("fel_invoice_requests", function (Blueprint $table) {
            $table->dropColumn('debitoFiscalIva');
            $table->dropColumn('creditoFiscalIva');
        });
    }
}
