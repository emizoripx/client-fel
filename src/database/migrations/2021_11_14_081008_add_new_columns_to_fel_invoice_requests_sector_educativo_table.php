<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToFelInvoiceRequestsSectorEducativoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->string('nombreEstudiante')->nullable();
            $table->string('periodoFacturado')->nullable();
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
            $table->dropColumn('nombreEstudiante');
            $table->dropColumn('periodoFacturado');
        });
    }
}
