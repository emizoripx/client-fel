<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSearchColumnFelClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_clients', function (Blueprint $table) {
            $table->string('search_fields')->nullable();
            $table->index(['company_id','search_fields']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_clients', function (Blueprint $table) {
            $table->dropColumn('search_fields');
        });
    }
}
