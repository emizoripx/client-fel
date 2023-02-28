<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_templates', function (Blueprint $table) {
            $table->integer('pos_code')->unsigned()->nullable()->after('branch_code');

            $table->dropUnique('fel_templates_document_sector_code_company_id_branch_code_unique');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_templates', function (Blueprint $table) {
            $table->dropColumn('pos_code');
        });
    }
};
