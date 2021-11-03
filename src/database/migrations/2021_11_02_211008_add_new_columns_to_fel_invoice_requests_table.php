<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToFelInvoiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->decimal('montoGiftCard', 20, 5)->nullable();
            $table->decimal('descuentoAdicional', 20, 5)->nullable();
            $table->integer('codigoExcepcion')->unsigned()->nullable();
            $table->string('cafc')->nullable();
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
            $table->dropColumn('montoGiftCard');
            $table->dropColumn('descuentoAdicional');
            $table->dropColumn('codigoExcepcion');
            $table->dropColumn('cafc');
        });
    }
}
