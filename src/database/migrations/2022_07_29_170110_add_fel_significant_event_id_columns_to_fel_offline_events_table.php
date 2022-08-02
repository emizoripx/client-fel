<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFelSignificantEventIdColumnsToFelOfflineEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_offline_events', function (Blueprint $table) {
            $table->integer('fel_significant_event_id')->unsigned()->nullable();
        });

        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->string('cuis', 100)->nullable();
            $table->string('cufd', 100)->nullable();
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
            $table->dropColumn('fel_significant_event_id');
            $table->dropColumn('cuis');
            $table->dropColumn('cufd');
        });

        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->dropColumn('cuis');
            $table->dropColumn('cufd');
        });
    }
}
