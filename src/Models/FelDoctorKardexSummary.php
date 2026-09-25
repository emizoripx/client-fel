<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class FelDoctorKardexSummary extends Model
{
    protected $table = 'fel_doctor_kardex_summaries';

    protected $fillable = [
        'company_id',
        'doctor_id',
        'total_procedimientos',
        'total_monto_generado',
        'promedio_por_intervencion',
        'primer_servicio',
        'ultimo_servicio',
    ];
}
