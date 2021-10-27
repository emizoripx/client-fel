<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCodigoActividadEconomicaFelSyncProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("alter table fel_sync_products modify column codigo_actividad_economica  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL");
        \DB::statement("alter table fel_sync_products modify column codigo_producto_sin  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL");
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
