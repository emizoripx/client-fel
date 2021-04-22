<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FelPOS extends Model
{
    protected $table = 'fel_pos';

    protected $guarded = [];


    public static function existsPOS($company_id, $branch_code, $pos_code){
        $pos = self::where('company_id', $company_id)->where('codigoSucursal', $branch_code)->where('codigo', $pos_code)->first();

        return is_null($pos);
    }
}
