<?php
namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;
use Hashids\Hashids;
class FelSyncProduct extends Model
{
    protected $guarded = [];

    public static function getByCompanyId($company_id) {
        return self::where('company_id', $company_id)->get();
    }
    public function getIdOriginAttribute()
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        return $hashid->encode($this->attributes['id_origin']);
    }
    public function getCompanyIdAttribute()
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        return $hashid->encode($this->attributes['company_id']);
    }
}