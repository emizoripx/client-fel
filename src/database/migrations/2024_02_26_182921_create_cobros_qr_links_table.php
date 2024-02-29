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
        Schema::create('cobros_qr_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("client_id");
            $table->string("imei", 20);
            $table->unique(['client_id','imei']);
            $table->index("imei");
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
        Schema::dropIfExists('cobros_qr_links');
    }
};
