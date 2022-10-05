<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeAdditionalInformationColumnToFelInvoiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->text('informacionAdicional')->nullable()->change();
            $table->text('numeroDescripcionPaquetesBultos')->nullable()->change();
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
            $table->string('informacionAdicional')->nullable()->change();
            $table->string('numeroDescripcionPaquetesBultos')->nullable()->change();
        });
    }
}
