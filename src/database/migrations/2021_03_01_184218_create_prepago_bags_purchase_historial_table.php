<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrepagoBagsPurchaseHistorialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepago_bags_purchase_historial', function (Blueprint $table) {
            $table->id();
            $table->dateTime('purchase_date');
            $table->integer('account_id');
            $table->integer('number_invoices');
            $table->integer('number_invoices_before');
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
        Schema::dropIfExists('prepago_bags_purchase_historial');
    }
}
