<?php 
namespace EmizorIpx\ClientFel\Models;


use EmizorIpx\ClientFel\Traits\DecodeHashIds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FelClient extends Model {

    use SoftDeletes;
    use DecodeHashIds;
    use HasFactory;
    protected $table = 'fel_clients';
    protected $guarded =[];

    const CI = 1;
    const CEX = 2;
    const PAS = 3;
    const OD = 4;
    const NIT = 5;

    protected static function newFactory(){
        return \EmizorIpx\ClientFel\Database\Factories\FelClientFactory::new();
    }

    public static function getByCompanyId($company_id) 
    {
        return self::withTrashed()->where('company_id', $company_id)->get();
    }

    public function getFieldTypeDocument(){
        switch ($this->type_document_id) {
            case self::CI:
                return "CI";
                break;
            case self::CEX:
                return "CEX";
                break;
            case self::PAS:
                return "PAS";
                break;
            case self::OD:
                return "OD";
                break;
            case self::NIT:
                return "NIT";
                break;
            
        }
    }
  
}