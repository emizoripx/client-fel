<?php

namespace EmizorIpx\ClientFel\Models;

use EmizorIpx\ClientFel\Traits\DecodeHashIds;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;

class FelInvoiceRequest extends Model
{
    use DecodeHashIds;
    protected $table = "fel_invoice_requests";

    protected $guarded = [];


    protected $cast =[
        'detalles' => 'array'
    ];

    public function getDetallesAttribute()
    {
       return json_decode($this->attributes['detalles'],true);
    }

    public function saveCuf($value) 
    {
        $this->cuf = $value;
        $this->save();
    }

    public static function findByIdOrigin($id_origin)
    {
        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $id_origin_decode = $hashid->decode($id_origin)[0];
        
        return self::whereIdOrigin($id_origin_decode)->first();
    }

    public static function getByCompanyId($company_id)
    {
        return self::where('company_id', $company_id)->get();
    }
}
