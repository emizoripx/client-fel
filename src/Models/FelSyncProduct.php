<?php
namespace EmizorIpx\ClientFel\Models;

use EmizorIpx\ClientFel\Traits\DecodeHashIds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FelSyncProduct extends Model
{
    use SoftDeletes;

    use DecodeHashIds;
    protected $guarded = [];

    public static function getByCompanyId($company_id) {
        return self::withTrashed()->where('company_id', $company_id)->get();
    }

}