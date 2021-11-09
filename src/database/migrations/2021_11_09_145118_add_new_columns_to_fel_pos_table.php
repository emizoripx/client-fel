<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToFelPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_pos', function (Blueprint $table) {
            $table->string('numeroContrato')->nullable();
            $table->string('nitComisionista')->nullable();
            $table->string('fechaInicio')->nullable();
            $table->string('fechaFin')->nullable();
            $table->integer('tipoPos')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_pos', function (Blueprint $table) {
            $table->dropColumn('numeroContrato');
            $table->dropColumn('nitComisionista');
            $table->dropColumn('fechaInicio');
            $table->dropColumn('fechaFin');
            $table->dropColumn('tipoPos');
        });
    }
}
