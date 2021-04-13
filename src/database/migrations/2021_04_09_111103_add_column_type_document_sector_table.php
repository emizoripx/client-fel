<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTypeDocumentSectorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function(Blueprint $table){
            $table->unsignedInteger('type_document_sector_id')->default(1);
            $table->string('type_invoice')->default('Con derecho a Crédito Fiscal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->dropColumn('type_document_sector_id');
            $table->dropColumn('type_invoice');
        });
    }
}
