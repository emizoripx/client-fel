<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressColumnToFelBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_branches', function (Blueprint $table) {
            $table->string('municipio', 100)->nullable()->after('company_id');
            $table->string('ciudad', 100)->nullable()->after('company_id');
            $table->string('pais', 100)->nullable()->after('company_id');
            $table->string('zona', 100)->nullable()->after('company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_branches', function (Blueprint $table) {
            $table->dropColumn('municipio');
            $table->dropColumn('ciudad');
            $table->dropColumn('pais');
            $table->dropColumn('zone');
        });
    }
}
