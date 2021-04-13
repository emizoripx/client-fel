<?php 
namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class FelActivity extends Model {
    protected $table = 'fel_activities';
    protected $guarded =[];

    public static function getDescriptionActivity($code){
        return self::where('codigo', $code)->first()->descripcion;
    }

}