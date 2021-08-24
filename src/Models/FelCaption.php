<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class FelCaption extends Model
{
    protected $table = 'fel_captions';
    protected $guarded =[];

    const PARAMETRIC_OFFLINE = '“Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido fuera de línea, verifique su envío con su proveedor o en la página web <a href="#">www.impuestos.gob.bo</a>.”';
    const PARAMETRIC_ONLINE = '"Este documento es una impresión de un Documento Digital emitido en una Modalidad de Facturación en Linea". La información puede ser verificada a través del Código QR que forma parte del formato de la representación gráfica.';

    public static function getCaptionDescription($code){
        return self::where('codigo', $code)->first()->descripcion;
    }
}
