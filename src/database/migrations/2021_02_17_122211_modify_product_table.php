<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('fel_sync_products');

        Schema::create('fel_sync_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("company_id");
            $table->unsignedInteger('id_origin');
            $table->unsignedInteger("codigo_producto");
            $table->unsignedInteger("codigo_product_sin");
            $table->unsignedInteger("codigo_actividad_economica");
            $table->unsignedInteger("codigo_unidad");
            $table->string("nombre_unidad");
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
    }
}
