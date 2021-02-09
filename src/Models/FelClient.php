<?php 
namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class FelClient extends Model {
    protected $table = 'fel_clients';
    protected $guarded =[];


    public static function getByCompanyId($company_id) 
    {
        return self::where('company_id', $company_id)->get();
    }
}