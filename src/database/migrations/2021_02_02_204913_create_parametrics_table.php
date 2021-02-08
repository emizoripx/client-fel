<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateParametricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Motivos de anulación
        Schema::create('fel_revocation_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('codigo');
            $table->string('descripcion');
            $table->timestamps();
        });
        
        // Paises
        Schema::create('fel_countries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('codigo');
            $table->string('descripcion');
            $table->timestamps();
        });

        // Tipo de documento de identidad
        Schema::create('fel_identity_document_types', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('codigo');
            $table->string('descripcion');
            $table->timestamps();
        });
        
        // Metodos de pago
        Schema::create('fel_payment_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('codigo');
            $table->string('descripcion');
            $table->timestamps();
        });

        // Monedas
        Schema::create('fel_currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('codigo');
            $table->string('descripcion');
            $table->timestamps();
        });

        // Unidades de medida
        Schema::create('fel_units', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('codigo');
            $table->string('descripcion');
            $table->timestamps();
        });

        // By Company

        Schema::create('fel_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('codigo');
            $table->unsignedInteger('company_id');
            $table->string('descripcion');
            $table->timestamps();

        });


        // Leyendas de factura
        Schema::create('fel_captions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo');
            $table->string('descripcion');
            $table->unsignedInteger('company_id');
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
        Schema::dropIfExists('fel_revocation_reasons');
        Schema::dropIfExists('fel_captions');
        Schema::dropIfExists('fel_activities');
        Schema::dropIfExists('fel_units');
        Schema::dropIfExists('fel_currencies');
        Schema::dropIfExists('fel_payment_methods');
        Schema::dropIfExists('fel_identity_document_types');
        Schema::dropIfExists('fel_countries');
        
    }
}
