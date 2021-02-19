<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFelInvoiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_invoice_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('id_origin');
            $table->unsignedInteger("codigoMetodoPago");
            $table->unsignedInteger("codigoLeyenda");
            $table->unsignedInteger("numeroFactura");
            $table->string("fechaEmision");
            $table->string("nombreRazonSocial");
            $table->unsignedInteger("codigoTipoDocumentoIdentidad");
            $table->string("numeroDocumento");
            $table->string("complemento")->nullable();
            $table->string("codigoCliente");
            $table->string('emailCliente')->nullable();
            $table->json('telefonoCliente')->nullable();
            $table->unsignedInteger("codigoPuntoVenta");
            $table->string('numeroTarjeta')->nullable();
            $table->unsignedInteger("codigoMoneda");
            $table->json('extras')->nullable();
            $table->unsignedInteger("tipoCambio");
            $table->decimal("montoTotal",20,5);
            $table->decimal("montoTotalMoneda",20,5);
            $table->decimal("montoTotalSujetoIva",20,5);
            $table->string("usuario");
            $table->json("detalles");
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fel_invoice_requests');
    }
}
