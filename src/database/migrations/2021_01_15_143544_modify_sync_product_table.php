<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifySyncProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('fel_sync_products', function(Blueprint $table) {
            $table->dropColumn('product_client_id');
            $table->dropColumn('product_fel_id');
        });

        Schema::table('fel_sync_products', function(Blueprint $table) {
            $table->string('codigoProducto');
            $table->string('codigoProductoSIN');
            $table->string('codigoActividadEconomica');
            $table->string('descripcion')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_sync_products', function(Blueprint $table) {
            $table->dropColumn('codigoProducto');
            $table->dropColumn('codigoProductoSIN');
            $table->dropColumn('codigoActividadEconomica');
            $table->dropColumn('descripcion');
        });
    }
}
