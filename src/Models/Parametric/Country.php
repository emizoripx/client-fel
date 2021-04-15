<?php

namespace EmizorIpx\ClientFel\Models\Parametric;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'fel_countries';
    protected $guarded =[];

    public static function getDescriptionCountry($code){
        return self::findOrFail($code)->descripcion;
    }
}
