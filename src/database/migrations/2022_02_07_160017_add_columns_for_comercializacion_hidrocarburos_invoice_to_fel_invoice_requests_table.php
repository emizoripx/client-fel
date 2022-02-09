<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsForComercializacionHidrocarburosInvoiceToFelInvoiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->string('placaVehiculo', 12)->nullable();
            $table->string('tipoEnvase', 50)->nullable();
            $table->decimal('montoTotalSujetoIvaLey317', 25, 5)->nullable();
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
            $table->dropColumn('placaVehiculo');
            $table->dropColumn('tipoEnvase');
            $table->dropColumn('montoTotalSujetoIvaLey317');
        });
    }
}
