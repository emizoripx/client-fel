<?php

namespace EmizorIpx\ClientFel\Models\Parametric;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'fel_currencies';
    protected $guarded =[];


    public static function getCurrecyDescription($code){
        return self::findOrFail($code)->descripcion;
    }
}
