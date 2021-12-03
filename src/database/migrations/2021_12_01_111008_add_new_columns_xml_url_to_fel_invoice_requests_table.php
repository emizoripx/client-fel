<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsXmlUrlToFelInvoiceRequestsTable extends Migration
{
    public function up()
    {

        Schema::table("fel_invoice_requests", function (Blueprint $table) {
            $table->string("xml_url")->nullable();
        });
    }


    public function down()
    {
        Schema::table("fel_invoice_requests", function (Blueprint $table) {
            $table->dropColumn("xml_url");
        });
    }
}
