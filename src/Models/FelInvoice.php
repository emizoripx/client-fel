<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class FelInvoice extends Model {

    protected $table = 'fel_invoices';

    protected $fillable =[
        'estado',
        'codigoRecepcion',
        'codigoEstado',
        'tipEmision',
        'nitEmisor',
        'numeroFactura',
        'cuf',
        'cufd',
        'sucursal',
        'direccion',
        'codigoPuntoVenta',
        'fechaEmision',
        'nombreRazonSocial',
        'documentoIdentidad',
        'numeroDocumento',
        'complemento',
        'codigoCliente',
        'metodoPago',
        'numeroTarjeta',
        'montoTotal',
        'moneda',
        'montoTotalMoneda',
        'leyenda',
        'documentoSector',
        'extras',
        'pdf_url',
        'errores',
        'montoTotalSujetoIva',
        'tipoCambio',
        'detalle',
        'id_origin'
    ];

    protected $casts = [
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
        'sucursal' => 'json',
        'documentoIdentidad'  => 'json',
        'metodoPago' => 'json',
        'moneda' => 'json',
        'documentoSector' => 'json',
        'extras' => 'json',
        'errores' => 'json',
        'detalle' => 'json'
    ];
}