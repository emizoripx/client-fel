<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class FelInvoice extends Model {


    protected $guarded =[];

    protected $casts = [
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
        'sucursal' => 'object',
        'documentoIdentidad'  => 'object',
        'metodoPago' => 'object',
        'moneda' => 'object',
        'documentoSector' => 'object',
        'extras' => 'object',
        'errores' => 'object',
        'detalle' => 'object'
    ];


}