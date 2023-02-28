<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('nit');
            $table->timestamps();
        });
        Schema::create('policies_invoices' , function(Blueprint $table){
            $table->id();
            $table->unsignedInteger('fel_invoice_request_id');
            $table->string('policy_code')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agencies');
        Schema::dropIfExists('policies_invoices');
    }
}
