<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataSpecificBySectorColumnToFelInvoiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->json('data_specific_by_sector')->nullable();
        });

        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            
            $table->dropColumn('nombreRepresentanteLegal');
            $table->dropColumn('nombrePropietario');
            $table->dropColumn('condicionPago');
            $table->dropColumn('periodoEntrega');
            $table->dropColumn('montoIehd');

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
            $table->dropColumn('data_specific_by_sector');
        });
    }
}
