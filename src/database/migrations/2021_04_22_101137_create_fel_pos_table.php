<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFelPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_pos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('codigo');
            $table->string('descripcion', 500);
            $table->unsignedInteger('codigoSucursal');
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
        Schema::dropIfExists('fel_pos');
    }
}
