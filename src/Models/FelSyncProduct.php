<?php
namespace EmizorIpx\ClientFel\Models;


use EmizorIpx\ClientFel\Traits\DecodeHashIds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FelSyncProduct extends Model
{
    use SoftDeletes;

    use HasFactory;

    use DecodeHashIds;
    protected $guarded = [];

    protected static function newFactory(){
        return \EmizorIpx\ClientFel\Database\Factories\FelSyncProductFactory::new();
    }

    public static function getByCompanyId($company_id) {
        return self::withTrashed()->where('company_id', $company_id)->get();
    }

}