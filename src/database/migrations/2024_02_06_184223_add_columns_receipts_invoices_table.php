<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsReceiptsInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->json("document_data")->nullable();
            $table->string("document_search")->nullable()->index();
            $table->enum('document_type', ["receipt", "invoice"])->default("invoice");
            $table->index(["company_id", "document_type"]);
            $table->index(["company_id", "document_type", "created_at"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
