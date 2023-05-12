<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefactorClientFelTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // columns for searching
            $table->string('factura_ticket');
            $table->string('numeroFactura');
            $table->unsignedInteger('prefactura_number');
            $table->unsignedInteger("document_number");
            $table->unsignedInteger('type_document_sector_id')->nullable();
            $table->string('type_invoice');
            $table->unsignedInteger('codigoMetodoPago');
            $table->string('fechaEmision');
            $table->unsignedInteger('codigoPuntoVenta');
            $table->unsignedInteger('codigoSucursal');
            $table->string('nombreRazonSocial');
            $table->unsignedInteger('codigoTipoDocumentoIdentidad');
            $table->string('numeroDocumento');
            $table->string('complemento');
            $table->string('emailCliente');
            $table->string('telefonoCliente');
            $table->string('cafc');
            $table->unsignedInteger('typeDocument');
            $table->string('codigoExcepcion');
            $table->unsignedInteger('codigoMoneda');
            $table->unsignedInteger('codigoCliente');
            $table->string('codigoLeyenda');
            $table->string('usuario');
            $table->json('extras');
            
            // datos de nota de credito debito
            $table->string('numeroAutorizacionCuf');
            $table->unsignedInteger('factura_original_id');
            $table->unsignedInteger('facturaExterna');
            $table->unsignedInteger('numeroFacturaOriginal');
            
            // totales
            $table->decimal('descuentoAdicional', 20, 8);
            $table->decimal('montoGiftCard', 20, 8)->nullable();
            $table->decimal('montoTotalSujetoIva', 20, 8)->nullable();
            $table->decimal('montoTotal', 20, 8)->nullable();
            $table->decimal('montoTotalMoneda', 20, 8)->nullable();
            $table->decimal('tipoCambio', 20, 8)->nullable();
            $table->decimal('montoDescuentoCreditoDebito', 20, 8)->nullable();
            $table->decimal('montoEfectivoCreditoDebito', 20, 8)->nullable();

            $table->unsignedInteger('codigoEstado')->nullable();
            $table->unsignedInteger('revocation_reason_code')->nullable();
            $table->unsignedInteger('revocated_by')->nullable();
            $table->unsignedInteger('emitted_by')->nullable();
            $table->unsignedInteger('package_id')->nullable();
            $table->unsignedInteger('index_package')->nullable();
            $table->string('numeroTarjeta')->nullable();
            $table->string('cuf')->nullable();
            $table->string('codigoActividad')->nullable();
            $table->string('estado')->nullable();
            $table->string('errores')->nullable();
            $table->string('urlSin')->nullable();
            $table->string('search_fields')->nullable();
            $table->string('ack_ticket')->nullable();
            $table->string('uuid_package')->nullable();

            $table->json('detalles');
            $table->json('external_invoice_data');
            // columns in JSON
            $table->json('data_specific_by_sector');

        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string("document_number");
            $table->string("business_name");
            $table->unsignedInteger("type_document_id");
            $table->string("complement");
            $table->string("search_fields");
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string("codigo_producto");
            $table->string("codigo_actividad_economica");
            $table->unsignedInteger("codigo_unidad");
            $table->string("nombre_unidad");
            $table->string("codigo_producto_sin");
            $table->string("codigo_nandina");
            $table->json("additional_data");
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
