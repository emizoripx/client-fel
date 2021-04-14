<?php

namespace EmizorIpx\ClientFel\Models\Parametric;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'fel_units';
    protected $guarded =[];


    public static function getUnitDescription($code){
        return self::findOrFail($code)->descripcion;
    }
}
