<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsFelSyncProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_sync_products', function (Blueprint $table) {
            $table->string('codigo_unidad');
            $table->string('nombre_unidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_sync_products', function (Blueprint $table) {
            $table->dropColumn('codigo_unidad');
            $table->dropColumn('nombre_unidad');
        });
    }
}
