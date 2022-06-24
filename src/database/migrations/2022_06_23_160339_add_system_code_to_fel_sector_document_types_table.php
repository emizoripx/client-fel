<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSystemCodeToFelSectorDocumentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_sector_document_types', function (Blueprint $table) {
            $table->string('codigoSistema', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_sector_document_types', function (Blueprint $table) {
            $table->dropColumn('codigoSistema');
        });
    }
}
