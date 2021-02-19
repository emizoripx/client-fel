<?php 
namespace EmizorIpx\ClientFel\Models;

use EmizorIpx\ClientFel\Traits\DecodeHashIds;

use Illuminate\Database\Eloquent\Model;

class FelClient extends Model {

    use DecodeHashIds;
    protected $table = 'fel_clients';
    protected $guarded =[];

    public static function getByCompanyId($company_id) 
    {
        return self::where('company_id', $company_id)->get();
    }
  
}