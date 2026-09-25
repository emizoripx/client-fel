<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class FelDoctorKardexMovement extends Model
{
    protected $table = 'fel_doctor_kardex_movements';

    protected $fillable = [
        'company_id',
        'doctor_id',
        'invoice_id',
        'invoice_number',
        'invoice_date',
        'client_name',
        'client_nit',
        'procedimiento',
        'quantity',
        'unit_price',
        'line_total',
        'nro_quirofano',
        'nro_factura_medico',
        'especialidad_medico',
        'estado_factura',
    ];
}
