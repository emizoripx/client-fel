<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFelActivityDocumentSectorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_activity_document_sector', function (Blueprint $table) {
            $table->id();
            $table->string('codigoActividad', 100)->nullable();
            $table->string('actividad', 500)->nullable();
            $table->string('codigoDocumentoSector')->nullable();
            $table->string('tipoDocumentoSector', 500)->nullable();
            $table->string('documentoSector', 500)->nullable();
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
        Schema::dropIfExists('fel_activity_document_sector');
    }
}
