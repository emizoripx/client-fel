<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFelDoctorKardexTables extends Migration
{
    public function up()
    {
        Schema::create('fel_doctor_kardex_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->unsignedBigInteger('doctor_id')->index();
            
            $table->integer('total_procedimientos')->default(0);
            $table->decimal('total_monto_generado', 14, 2)->default(0);
            $table->decimal('promedio_por_intervencion', 14, 2)->default(0);
            
            $table->date('primer_servicio')->nullable();
            $table->date('ultimo_servicio')->nullable();
            
            $table->timestamps();
        });

        Schema::create('fel_doctor_kardex_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->unsignedBigInteger('doctor_id')->index();
            $table->unsignedBigInteger('invoice_id')->index();
            
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            
            $table->string('client_name')->nullable();
            $table->string('client_nit')->nullable();
            
            $table->string('procedimiento')->nullable();
            $table->decimal('quantity', 14, 2)->default(1);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            
            $table->string('nro_quirofano')->nullable();
            $table->string('nro_factura_medico')->nullable();
            $table->string('especialidad_medico')->nullable();
            $table->string('estado_factura')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fel_doctor_kardex_movements');
        Schema::dropIfExists('fel_doctor_kardex_summaries');
    }
}
