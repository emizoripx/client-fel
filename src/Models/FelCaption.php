<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class FelCaption extends Model
{
    protected $table = 'fel_captions';
    protected $guarded =[];

    public static function getCaptionDescription($code){
        return self::where('codigo', $code)->first()->descripcion;
    }
}
