<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceWhatsappMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('client_contact_id');
            $table->unsignedInteger('invoice_id');
            $table->string('message_id')->nullable();
            $table->text('message')->nullable();
            $table->string('number_phone')->nullable();
            $table->boolean('authorize_to_sent')->nullable()->default(true);
            $table->string('rejection_reason')->nullable();
            $table->string('status')->nullable();
            $table->string('state')->nullable();
            $table->string('status_description')->nullable();
            $table->dateTime('send_date')->nullable();
            $table->dateTime('delivered_date')->nullable();
            $table->text('errors')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('client_contact_id')->references('id')->on('client_contacts');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_whatsapp_messages');
    }
}
