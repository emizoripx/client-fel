<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnFelProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_sync_products', function(Blueprint $table){
            $table->dropColumn('codigo_product_sin');
        });
        Schema::table('fel_sync_products', function(Blueprint $table){
            $table->unsignedInteger('codigo_producto_sin');
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
