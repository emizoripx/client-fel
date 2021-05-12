<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsDebitoCreditoFelInvoiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->string("factura_original_id")->nullable(); // old invoice cuf
            $table->unsignedInteger("numeroFacturaOriginal")->nullable(); // old invoice
            $table->decimal("montoDescuentoCreditoDebito", 20, 5)->nullable(); // dicount cuf
            $table->decimal("montoEfectivoCreditoDebito", 20, 5)->nullable(); // credito fiscal actual
            // creating index unique, cuf must be unique
            $table->unique('cuf');
            $table->index('id_origin');
            $table->index('company_id');
            $table->index('factura_original_id');
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
            $table->dropColumn("numeroFacturaOriginal");
            $table->dropColumn("montoDescuentoCreditoDebito");
            $table->dropColumn("montoEfectivoCreditoDebito");
        });
    }
}
