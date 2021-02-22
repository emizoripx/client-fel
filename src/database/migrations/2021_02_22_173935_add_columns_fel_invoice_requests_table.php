<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsFelInvoiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function(Blueprint $table) {
            $table->unsignedInteger('codigoActividad')->default(0);
            $table->unsignedInteger('codigoEstado')->nullable();
            $table->string('estado')->nullable();
            $table->json('errores')->nullable();
            
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
            $table->dropColumn('codigoActividad');
            $table->dropColumn('codigoEstado');
            $table->dropColumn('estado');
            $table->dropColumn('errors');
        });
    }
}
