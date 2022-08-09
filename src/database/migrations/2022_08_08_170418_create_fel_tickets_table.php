<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFelTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('branch_code')->unsigned();
            $table->unsignedBigInteger('product_id');
            $table->integer('turno')->unsigned();
            $table->integer('start_ticket_number')->unsigned()->nullable();
            $table->integer('final_ticket_number')->unsigned()->nullable();
            $table->integer('quantity_tickets_sold')->unsigned()->default(0);
            $table->decimal('unit_price', 20, 5)->nullable();
            $table->decimal('total', 20, 5)->nullable();
            $table->dateTime('checked_at')->nullable();
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
        Schema::dropIfExists('fel_tickets');
    }
}
