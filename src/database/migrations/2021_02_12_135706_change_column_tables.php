<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_clients' , function(Blueprint $table) {
            $table->dropColumn('document_identidad');
        });
        Schema::table('fel_clients' , function(Blueprint $table) {
            $table->unsignedInteger('type_document_id');
            $table->unsignedInteger('document_number');
            $table->string('business_name');
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
            $table->string('document_identidad');
        });
        Schema::table('fel_clients', function (Blueprint $table) {
            $table->dropColumn('type_document_id');
            $table->dropColumn('document_number');
            $table->dropColumn('business_name');
        });
    }
}
