<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComplementColumnToFelClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_clients', function (Blueprint $table) {
            $table->string('complement', 10)->after('document_number')->nullable();
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
            $table->dropColumn('complement');
        });
    }
}
