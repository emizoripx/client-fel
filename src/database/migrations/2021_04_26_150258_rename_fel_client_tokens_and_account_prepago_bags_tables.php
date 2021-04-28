<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFelClientTokensAndAccountPrepagoBagsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('fel_client_tokens', 'fel_company_tokens');

        Schema::rename('account_prepago_bags', 'fel_company');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('fel_company_tokens', 'fel_client_tokens');
        Schema::rename('fel_company', 'account_prepago_bags');
    }
}
