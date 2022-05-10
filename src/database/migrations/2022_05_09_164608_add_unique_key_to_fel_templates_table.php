<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueKeyToFelTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_templates', function (Blueprint $table) {
            $table->unique(['document_sector_code', 'company_id', 'branch_code']);
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
            $table->dropUnique('fel_templates_document_sector_code_company_id_branch_code_unique');
        });
    }
}
