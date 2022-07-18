<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFelReportRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_report_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->integer('custom_report_id')->unsigned();
            $table->string('name', 250)->nullable();
            $table->string('entity', 250)->nullable();
            $table->string('request_parameters', 500)->nullable();
            $table->integer('status')->unsigned()->default(1);
            $table->string('s3_filepath', 500)->nullable();
            $table->string('filename', 100)->nullable();
            $table->date('report_date')->nullable();
            $table->dateTime('registered_at')->nullable();
            $table->dateTime('start_process_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('fel_report_requests');
    }
}
