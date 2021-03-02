<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFelSectorDocumentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_sector_document_types', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('codigoSucursal', 10)->nullable()->default('0');
            $table->string('documentoSector', 255)->nullable();
            $table->string('tipoFactura', 255)->nullable();
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
        Schema::dropIfExists('fel_sector_document_types');
    }
}
