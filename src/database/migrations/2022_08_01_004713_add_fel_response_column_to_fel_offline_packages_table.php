<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFelResponseColumnToFelOfflinePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_offline_packages', function (Blueprint $table) {
            $table->text('fel_response')->nullable();
            $table->boolean('has_errors')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_offline_packages', function (Blueprint $table) {
            $table->dropColumn('fel_response');
            $table->dropColumn('has_errors');
        });
    }
}
