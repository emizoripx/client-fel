<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToFelInvoiceRequestsOtherDocumentSectorTable extends Migration
{
    public function up()
    {

        Schema::table("fel_invoice_requests", function (Blueprint $table) {
            $table->string("ciudad")->nullable();
            $table->string("nombrePropietario")->nullable();
            $table->string("nombreRepresentanteLegal")->nullable();
            $table->string("condicionPago")->nullable();
            $table->string("periodoEntrega")->nullable();
            $table->string("montoIehd")->nullable();

            // servicios basicos

            $table->string("mes")->nullable();
            $table->string("gestion")->nullable();
            $table->string("zona")->nullable();
            $table->string("numeroMedidor")->nullable();
            $table->string("domicilioCliente")->nullable();
            $table->string("consumoPeriodo")->nullable();
            $table->string("beneficiarioLey1886")->nullable();
            $table->string("montoDescuentoLey1886")->nullable();
            $table->string("montoDescuentoTarifaDignidad")->nullable();
            $table->string("tasaAseo")->nullable();
            $table->string("tasaAlumbrado")->nullable();
            $table->string("ajusteNoSujetoIva")->nullable();
            $table->string("detalleAjusteNoSujetoIva")->nullable();
            $table->string("ajusteSujetoIva")->nullable();
            $table->string("detalleAjusteSujetoIva")->nullable();
            $table->string("otrosPagosNoSujetoIva")->nullable();
            $table->string("detalleOtrosPagosNoSujetoIva")->nullable();

            //hoteles

            $table->string("cantidadHuespedes")->nullable();
            $table->string("cantidadHabitaciones")->nullable();
            $table->string("cantidadMayores")->nullable();
            $table->string("cantidadMenores")->nullable();
            $table->string("fechaIngresoHospedaje")->nullable();
        });
    }


    public function down()
    {
        Schema::table("fel_invoice_requests", function (Blueprint $table) {
            $table->dropColumn("ciudad");
            $table->dropColumn("nombrePropietario");
            $table->dropColumn("nombreRepresentanteLegal");
            $table->dropColumn("condicionPago");
            $table->dropColumn("periodoEntrega");
            $table->dropColumn("montoIehd");

            $table->dropColumn("mes");
            $table->dropColumn("gestion");
            $table->dropColumn("zona");
            $table->dropColumn("numeroMedidor");
            $table->dropColumn("domicilioCliente");
            $table->dropColumn("consumoPeriodo");
            $table->dropColumn("beneficiarioLey1886");
            $table->dropColumn("montoDescuentoLey1886");
            $table->dropColumn("montoDescuentoTarifaDignidad");
            $table->dropColumn("tasaAseo");
            $table->dropColumn("tasaAlumbrado");
            $table->dropColumn("ajusteNoSujetoIva");
            $table->dropColumn("detalleAjusteNoSujetoIva");
            $table->dropColumn("ajusteSujetoIva");
            $table->dropColumn("detalleAjusteSujetoIva");
            $table->dropColumn("otrosPagosNoSujetoIva");
            $table->dropColumn("detalleOtrosPagosNoSujetoIva");

            //hoteles
            $table->dropColumn("cantidadHuespedes");
            $table->dropColumn("cantidadHabitaciones");
            $table->dropColumn("cantidadMayores");
            $table->dropColumn("cantidadMenores");
            $table->dropColumn("fechaIngresoHospedaje");
        });
    }
}
