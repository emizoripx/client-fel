<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsMissingForComercialExportacionFelInvoiceRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->decimal('totalGastosNacionalesFob',20,5)->change();
            $table->decimal('totalGastosInternacionales',20,5)->change();
            $table->json('costosGastosNacionales')->nullable();
            $table->json('costosGastosInternacionales')->nullable();
        });
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
