<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFelInvoiceStatusHistorialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_invoice_status_historial', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('fel_invoice_id')->nullable();
            $table->string('cuf', 300)->nullable();
            $table->string('estado', 100)->nullable();
            $table->unsignedInteger('codigo_estado')->nullable();
            $table->string('codigo_recepcion')->nullable();
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('codigo_motivo_anulacion')->nullable();
            $table->json('errors')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fel_invoice_status_historial');
    }
}
