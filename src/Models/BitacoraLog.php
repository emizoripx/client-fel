<?php
namespace EmizorIpx\ClientFel\Models;

use Carbon\Carbon;
use EmizorIpx\ClientFel\Utils\BitacoraType;
use Illuminate\Database\Eloquent\Model;

class BitacoraLog extends Model {

    protected $guarded =[];

    protected $table ='fel_bitacora_logs';

    const ERROR='ERROR';
    const WARNING='WARNING';
    const INFO='INFO';
    const REQUEST='REQUEST';

    protected static function boot()
    {
        parent::boot();
        static::created(function ($query) {
            // remove unused qr payment register 
            \DB::table('fel_bitacora_logs')
            ->whereRaw(" updated_at < now() - interval 7 DAY")
            ->delete();
        });
    }
    public static function getConstants()
    {
        $class = new \ReflectionClass(get_called_class());
       return array_values($class->getConstants());
       
    }

    public static function register( $type ,string $event, string $message) : void
    {
        if ( !in_array(strtoupper($type) , self::getConstants() ))
            return;

        self::create(
            [
                'event' => $event,
                'type' => strtoupper($type),
                'message' => $message,
                'created_at' => Carbon::now()
            ]
        );
    }


    public function getButtonTypeAttribute()
    {
        return BitacoraType::button($this->type);
    }


}