<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHostColumnFelClientTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_client_tokens', function (Blueprint $table) {
            $table->string('host', 500)->nullable();
        });

        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->dropColumn('host');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_client_tokens', function (Blueprint $table) {
            $table->dropColumn('host');
        });
    }
}
