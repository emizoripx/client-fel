<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCounterPostpagoToFelCompanyDocumentSectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_company_document_sectors', function (Blueprint $table) {
            $table->unsignedInteger('postpago_counter')->after('counter');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_company_document_sectors', function (Blueprint $table) {
            $table->dropColumn('postpago_counter');
        });
    }
}
