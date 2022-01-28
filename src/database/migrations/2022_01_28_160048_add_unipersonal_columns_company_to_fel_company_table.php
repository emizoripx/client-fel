<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnipersonalColumnsCompanyToFelCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_company', function (Blueprint $table) {
            $table->string('business_name')->nullable();
            $table->boolean('is_uniper')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_company', function (Blueprint $table) {
            $table->dropColumn('business_name');
            $table->dropColumn('is_uniper');
            
        });
    }
}
