<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReadDateToInvoiceWhatsappMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_whatsapp_messages', function (Blueprint $table) {
            $table->dateTime('read_date')->nullable()->after('delivered_date');
            $table->dateTime('dispatched_date')->nullable()->after('read_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_whatsapp_messages', function (Blueprint $table) {
            $table->dropColumn('read_date');
            $table->dropColumn('dispatched_date');

        });
    }
}
