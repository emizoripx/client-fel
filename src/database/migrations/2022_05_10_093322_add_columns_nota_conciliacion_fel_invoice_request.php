<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsNotaConciliacionFelInvoiceRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("fel_invoice_requests", function(Blueprint $table) {
            $table->json('external_invoice_data');
            $table->boolean('facturaExterna')->default(0);
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
            $table->dropColumn('external_invoice_data');
            $table->dropColumn('facturaExterna');
        });
    }
}
