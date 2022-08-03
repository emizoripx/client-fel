<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddErrorsColumnToFelOfflineEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_offline_events', function (Blueprint $table) {
            $table->text('errors')->nullable();
            $table->text('fel_response')->nullable();
            $table->text('fel_errors')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_offline_events', function (Blueprint $table) {
            $table->dropColumn('errors');
            $table->dropColumn('fel_response');
            $table->dropColumn('fel_errors');
        });
    }
}
