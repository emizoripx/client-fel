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
        Schema::create('company_user_terminals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("company_user_id");
            $table->string("terminal_code",6);
            $table->string("access_token", 64)->nullable();
            $table->string('imei', 32)->nullable();
            $table->string('serial_number', 32)->nullable();
            $table->datetime('expires_at')->nullable();
            $table->unsignedInteger('device_id')->nullable();
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
        Schema::dropIfExists('company_user_terminals');
    }
};
