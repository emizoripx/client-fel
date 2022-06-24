<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCufdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_cufd', function (Blueprint $table) {
            $table->id();
            $table->string('cufd', 255);
            $table->dateTime('validity');
            $table->string('control_code', 255);
            $table->string('system_code', 255);
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('branch_id');
            $table->integer('branch_code')->unsigned();
            $table->unsignedBigInteger('pos_id')->nullable();
            $table->integer('pos_code')->unsigned()->nullable();
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
        Schema::dropIfExists('cufd');
    }
}
