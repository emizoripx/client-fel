<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsDeletedAtFelSyncProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_sync_products', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('fel_clients', function (Blueprint $table) {
            $table->softDeletes();
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
            $table->dropSoftDeletes();
        });

        Schema::table('fel_clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
            
        });
    }
}
