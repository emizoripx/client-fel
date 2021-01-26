<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateFelInvoiceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::dropIfExists('fel_invoice_details');
        Schema::dropIfExists('fel_invoices');

        Schema::create('fel_invoices', function(Blueprint $table) {
            
            $table->increments('id');

            $table->string('id_origin');

            $table->string('estado');
            $table->string('codigoRecepcion')->nullable();
            $table->string('codigoEstado')->nullable();
            $table->json('tipEmision')->nullable();
            $table->unsignedInteger('nitEmisor');
            $table->unsignedInteger('numeroFactura');
            $table->string('cuf')->unique();
            $table->string('cufd');
            $table->json('sucursal')->nullable();
            $table->string('direccion');
            $table->string('codigoPuntoVenta')->nullable();
            $table->string('fechaEmision');
            $table->string('nombreRazonSocial');
            $table->json('documentoIdentidad')->nullable();
            $table->string('numeroDocumento');
            $table->string('complemento')->nullable();
            $table->string('codigoCliente')->nullable();
            $table->json('metodoPago')->nullable();
            $table->string('numeroTarjeta')->nullable();
            $table->string('montoTotal');
            $table->json('moneda')->nullable();
            $table->string('montoTotalMoneda');
            $table->string('leyenda');
            $table->json('documentoSector')->nullable();
            $table->json('extras')->nullable();
            $table->string('pdf_url')->nullable();
            $table->json('errores')->nullable();
            $table->string('montoTotalSujetoIva');
            $table->string('tipoCambio');
            $table->json('detalle');

            
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
        //
    }
}
