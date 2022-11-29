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
            $table->unique(['document_sector_code', 'company_id', 'branch_code', 'pos_code'], 'fel_templates_doc_code_company_id_b_code_p_code_unique');
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
            $table->dropUnique('fel_templates_doc_code_company_id_b_code_p_code_unique');
        });
    }
};
