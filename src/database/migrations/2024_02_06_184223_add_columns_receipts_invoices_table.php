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
            if (!Schema::hasColumn('invoices', 'document_data')) {
                $table->json("document_data")->nullable();
            }
            if (!Schema::hasColumn('invoices', 'document_search')) {
                $table->string("document_search")->nullable()->index();
            }
            if (!Schema::hasColumn('invoices', 'document_type')) {
                $table->enum('document_type', ["receipt", "invoice"])->default("invoice");
            }
            if (!Schema::hasColumn('invoices', 'company_id') || !Schema::hasColumn('invoices', 'document_type')) {
                $table->index(["company_id", "document_type"]);
            }
            if (!Schema::hasColumn('invoices', 'company_id') || !Schema::hasColumn('invoices', 'document_type') || !Schema::hasColumn('invoices', 'created_at')) {
                $table->index(["company_id", "document_type", "created_at"]);
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
    }
}
