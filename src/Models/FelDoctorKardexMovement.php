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

    protected $appends = ['hashed_invoice_id'];

    public function getHashedInvoiceIdAttribute()
    {
        if (!$this->invoice_id) return null;
        $hashid = new \Hashids\Hashids(config('ninja.hash_salt'), 10);
        return $hashid->encode($this->invoice_id);
    }
}
