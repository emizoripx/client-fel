<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientfelTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('fel_sync_products', function(Blueprint $table) {
            $table->increments('id');
            $table->string('product_client_id');
            $table->string('product_fel_id');
            $table->timestamps();
        });
        
        Schema::create('fel_invoices', function(Blueprint $table) {
            
            $table->increments('id');
            $table->string('state');
            $table->string('reception_code')->nullable();
            $table->string('pdf_url')->nullable();
            $table->string('cuis');
            $table->string('cufd');
            $table->string('cuf')->unique();
            $table->unsignedInteger('caption_code');
            $table->unsignedInteger('emission_type_code');
            $table->unsignedInteger('revocation_reason_code')->nullable();
            
            $table->json('extras')->nullable();
            $table->json('sin_errors')->nullable();

            // Factura Compra Venta
            $table->bigInteger('nitEmisor');
            $table->string('razonSocialEmisor');
            $table->string('municipio');
            $table->string('telefono');
            $table->unsignedInteger('numeroFactura');
            $table->string('codigoSucursal')->default(0); //Casa Matriz
            $table->string('direccion');
            $table->string('codigoPuntoVenta')->nullable();
            $table->string('fechaEmision');
            $table->string('nombreRazonSocial');
            $table->unsignedInteger('codigoTipoDocumentoIdentidad');
            $table->string('numeroDocumento');
            $table->string('complemento')->nullable();
            $table->string('codigoCliente');
            $table->unsignedInteger('codigoMetodoPago')->nullable();
            $table->unsignedInteger('numeroTarjeta')->nullable();
            $table->decimal('montoTotal', 25, 5)->nullable();
            $table->decimal('montoTotalSujetoIva', 25, 5)->nullable();
            $table->unsignedInteger('codigoMoneda')->nullable();
            $table->decimal('tipoCambio', 25, 5)->nullable();
            $table->decimal('montoTotalMoneda', 25, 5)->nullable();
            $table->string('leyenda');
            $table->string('usuario');
            $table->unsignedInteger('codigoDocumentoSector')->nullable();

            $table->timestamps();
        });

        Schema::create('fel_invoice_details', function(Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('fiscal_document_id');
            $table->json('extras')->nullable();
            
            // Factira compra venta
            $table->integer('actividadEconomica');
            $table->integer('codigoProductoSin');
            $table->string('codigoProducto');
            $table->string('descripcion');
            $table->decimal('cantidad', 25, 5)->nullable();
            $table->unsignedInteger('unidadMedida');
            $table->decimal('precioUnitario', 25, 5);
            $table->decimal('montoDescuento', 25, 5)->nullable();
            $table->decimal('subTotal', 25, 5);
            $table->string('numeroSerie')->nullable();
            $table->string('numeroImei')->nullable();

            $table->timestamps();
            $table->foreign('fiscal_document_id')->references('id')->on('fel_invoices');

        });

        Schema::create('fel_request_logs', function(Blueprint $table) {
            $table->increments('id');
            $table->string('entity');
            $table->integer('endity_id');
            $table->text('request');
            $table->timestamps();
        });

        Schema::create('fel_client_tokens', function(Blueprint $table) {
            $table->increments('id');
            $table->string('grant_type');  
            $table->string('client_id');  
            $table->string('client_secret');  
            $table->integer('account_id'); 
            $table->text('access_token')->nullable(); 
            $table->string('expires_in')->nullable(); 
            $table->string('token_type')->nullable(); 
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
        Schema::dropIfExists('fel_sync_products');
        Schema::dropIfExists('fel_invoice_details');
        Schema::dropIfExists('fel_invoices');
        Schema::dropIfExists('fel_request_logs');
        Schema::dropIfExists('fel_client_tokens');
    }
}
