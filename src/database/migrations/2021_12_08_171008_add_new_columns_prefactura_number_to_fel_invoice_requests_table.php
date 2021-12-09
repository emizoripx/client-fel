<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsPrefacturaNumberToFelInvoiceRequestsTable extends Migration
{
    public function up()
    {

        Schema::table("fel_invoice_requests", function (Blueprint $table) {
            $table->unsignedInteger("prefactura_number")->nullable();
        });
    }


    public function down()
    {
        Schema::table("fel_invoice_requests", function (Blueprint $table) {
            $table->dropColumn("prefactura_number");
        });
    }
}
