<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnComercialExportacionInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->string('incoterm_detalle')->nullable();
            $table->string('lugarDestino')->nullable();
            $table->json('totalGastosNacionalesFob')->nullable();
            $table->json('totalGastosInternacionales')->nullable();
            $table->string('numeroDescripcionPaquetesBultos')->nullable();
            $table->string('informacionAdicional')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->dropColumn('incoterm_detalle');
            $table->dropColumn('lugarDestino');
            $table->dropColumn('totalGastosNacionalesFob');
            $table->dropColumn('totalGastosInternacionales');
            $table->dropColumn('numeroDescripcionPaquetesBultos');
            $table->dropColumn('informacionAdicional');
        });
    }
}
