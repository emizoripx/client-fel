<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsExportacionFelInvoiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_invoice_requests', function(Blueprint $table) {
            $table->string("direccionComprador")->nullable();
            $table->string("ruex")->nullable();
            $table->string("nim")->nullable();
            $table->string("concentradoGranel")->nullable();
            $table->string("origen")->nullable();
            $table->string("puertoTransito")->nullable();
            $table->string("puertoDestino")->nullable();
            $table->string("incoterm")->nullable();
            $table->decimal("tipoCambioANB",5,2)->nullable();
            $table->string("numeroLote")->nullable();
            $table->unsignedInteger("paisDestino")->nullable();
            $table->decimal("kilosNetosHumedos",20,5)->nullable();
            $table->decimal("humedadPorcentaje",20,5)->nullable();
            $table->decimal("humedadValor",20,5)->nullable();
            $table->decimal("mermaPorcentaje",20,5)->nullable();
            $table->decimal("mermaValor",20,5)->nullable();
            $table->decimal("kilosNetosSecos",20,5)->nullable();
            $table->decimal("gastosRealizacion",20,5)->nullable();
            $table->json("otrosDatos")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schem::table('fel_invoice_requests', function (Blueprint $table) {
            $table->dropColumn("direccionComprador");
            $table->dropColumn("ruex");
            $table->dropColumn("nim");
            $table->dropColumn("concentradoGranel");
            $table->dropColumn("origen");
            $table->dropColumn("puertoTransito");
            $table->dropColumn("puertoDestino");
            $table->dropColumn("incoterm");
            $table->dropColumn("tipoCambioANB", 5, 2);
            $table->dropColumn("numeroLote");
            $table->dropColumn("paisDestino");
            $table->dropColumn("kilosNetosHumedos");
            $table->dropColumn("humedadPorcentaje");
            $table->dropColumn("humedadValor");
            $table->dropColumn("mermaPorcentaje");
            $table->dropColumn("mermaValor");
            $table->dropColumn("kilosNetosSecos");
            $table->dropColumn("gastosRealizacion");
            $table->dropColumn("otrosDatos");
        });
    }
}
