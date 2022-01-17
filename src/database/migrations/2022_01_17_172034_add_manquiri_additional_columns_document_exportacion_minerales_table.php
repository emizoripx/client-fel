<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManquiriAdditionalColumnsDocumentExportacionMineralesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->decimal('pesoBrutoKg',20,5)->nullable()->after('paisDestino');
            $table->decimal('pesoBrutoGr',20,5)->nullable()->after('pesoBrutoKg');
            $table->decimal('pesoNetoGr',20,5)->nullable()->after('pesoBrutoGr');
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
            $table->dropColumn('pesoBrutoKg');
            $table->dropColumn('pesoBrutoGr');
            $table->dropColumn('pesoNetoGr');
        });
    }
}
