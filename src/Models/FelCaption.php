<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class FelCaption extends Model
{
    protected $table = 'fel_captions';
    protected $guarded =[];

    const PARAMETRIC_OFFLINE = '“Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido fuera de línea, verifique su envío con su proveedor o en la página web <a href="https://www.impuestos.gob.bo"> www.impuestos.gob.bo </a> .”';
    const PARAMETRIC_ONLINE = '"Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido en una modalidad de facturación en línea".';

    public static function getCaptionDescription($code){
        return self::where('codigo', $code)->first()->descripcion;
    }
}
