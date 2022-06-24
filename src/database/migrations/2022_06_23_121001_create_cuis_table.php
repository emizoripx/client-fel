<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_cuis', function (Blueprint $table) {
            $table->id();
            $table->string('cuis', 100);
            $table->dateTime('validity');
            $table->string('system_code', 255)->nullable();
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
        Schema::dropIfExists('cuis');
    }
}
