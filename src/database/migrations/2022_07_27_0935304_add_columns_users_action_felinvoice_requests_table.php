<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsUsersActionFelinvoiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->unsignedInteger('emitted_by')->nullable();
            $table->unsignedInteger('revocated_by')->nullable();
        });

        \DB::statement("update fel_invoice_requests join invoices on fel_invoice_requests.id_origin = invoices.id set fel_invoice_requests.emitted_by = invoices.user_id where invoices.id > 0");
        \DB::statement("update fel_invoice_requests join invoices on fel_invoice_requests.id_origin = invoices.id set fel_invoice_requests.revocated_by = invoices.user_id where fel_invoice_requests.codigoEstado = 691");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->dropColumn('emitted_by');
            $table->dropColumn('revocated_by');
        });
    }
}
