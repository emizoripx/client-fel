<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FelUfv extends Model
{
    protected $table = 'fel_ufvs';

    protected $guarded = [];

    public function setFechaAttribute($value) {

        $date_format = Carbon::parse($value)->format('Y-m-d');

        return $this->attributes['fecha'] = $date_format;

    }

}
