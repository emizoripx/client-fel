<?php
namespace EmizorIpx\ClientFel\Traits;

use Hashids\Hashids;

trait DecodeHashIds {
    // public function getIdOriginAttribute()
    // {
    //     $hashid = new Hashids(config('ninja.hash_salt'), 10);

    //     return $hashid->encode($this->attributes['id_origin']);
    // }

    public function getCompanyIdAttribute()
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        return $hashid->encode($this->attributes['company_id']);
    }
}