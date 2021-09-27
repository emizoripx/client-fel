<?php

namespace EmizorIpx\ClientFel\Models\Parametric;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'fel_countries';
    protected $guarded =[];

    public static function getDescriptionCountry($code){
        if (!is_null($code) && $code > 0 && $code <= 208)
            return self::findOrFail($code)->descripcion;
        return "---";
    }
}
