<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Hashids\Hashids;

class FelInvoiceStatusHistorial extends Model
{
    use HasFactory;

    protected $table = 'fel_invoice_status_historial';
    protected $guarded = [];

    protected $casts = [
        'errors' => 'array'
    ];

    public static function registerHistorialInvoice($data, $errors = null, $codigoRecepcion = null){
   
        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        $input = [
            'fel_invoice_id' => $data->id ?? null,
            'cuf' => $data->cuf ?? null,
            'estado' => $data->estado ?? null,
            'codigo_estado' => $data->codigoEstado ?? null,
            'codigo_recepcion' => $codigoRecepcion,
            'company_id' => isset($data->company_id) ? $hashid->decode($data->company_id)[0] : null,
            'errors' => $errors,
            'codigo_motivo_anulacion' => $data->revocation_reason_code ?? null,
            'created_at' => Carbon::now()
        ];

        FelInvoiceStatusHistorial::create($input);
    }
}
