<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddColumnCodigoActividadFelCaptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("truncate fel_captions");
        Schema::table('fel_captions', function(Blueprint $table) {
            $table->unsignedInteger('codigoActividad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_captions', function(Blueprint $table) {
            $table->dropColumn('codigoActividad');
        });
    }
}
