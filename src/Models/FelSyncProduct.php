<?php
namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class FelSyncProduct extends Model
{
    protected $guarded = [];

    public static function getByCompanyId($company_id) {
        return self::where('company_id', $company_id)->get();
    }
}