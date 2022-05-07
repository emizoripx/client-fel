<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_templates', function (Blueprint $table) {
            $table->id();
            $table->string('display_name', 255);
            $table->integer('document_sector_code')->unsigned();
            $table->string('blade_resource', 255)->nullable();
            $table->boolean('header_custom')->default(false);
            $table->boolean('footer_custom')->default(false);
            $table->unsignedBigInteger('company_id');
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
        Schema::dropIfExists('templates');
    }
}
