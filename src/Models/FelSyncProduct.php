<?php
namespace EmizorIpx\ClientFel\Models;

use EmizorIpx\ClientFel\Traits\DecodeHashIds;

use Illuminate\Database\Eloquent\Model;

class FelSyncProduct extends Model
{

    use DecodeHashIds;
    protected $guarded = [];

    public static function getByCompanyId($company_id) {
        return self::where('company_id', $company_id)->get();
    }

}