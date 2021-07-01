<?php

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDataFelInvoiceRequestTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("fel_invoice_requests", function(Blueprint $table){
            $table->string('codigoActividad')->change();
        });

        
        FelInvoiceRequest::cursor()->each( function ($invoice) {
            if (strlen($invoice->codigoActividad) < 6){

                $invoice->codigoActividad = str_pad($invoice->codigoActividad, 6, "0", STR_PAD_LEFT);
                $invoice->save();
            }

        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
