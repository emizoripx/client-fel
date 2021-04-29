<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;
use Hashids\Hashids;

class FelBranch extends Model
{

    protected $table = 'fel_branches';

    protected $guarded = [];

    public static function existsBranch($company_id, $branch_code){
        $branch = self::where('company_id', $company_id)->where('codigo', $branch_code)->first();

        return is_null($branch);
    }


    public function fel_pos(){
        return $this->hasMany(FelPOS::class, 'branch_id', 'id');
    }

    public static function getBranchAddress( $company_id, $branch_code){

        $hashids = new Hashids(config('ninja.hash_salt'), 10);
        $companyIdDecoded = $hashids->decode($company_id)[0];

        $branch = self::where('company_id', $companyIdDecoded)->where('codigo', $branch_code)->first();

        return $branch->ciudad. ' - '.$branch->pais;
    }
    public static function getBranchZone( $company_id, $branch_code){

        $hashids = new Hashids(config('ninja.hash_salt'), 10);
        $companyIdDecoded = $hashids->decode($company_id)[0];

        $branch = self::where('company_id', $companyIdDecoded)->where('codigo', $branch_code)->first();

        return is_null($branch) ? '' : $branch->zona;
    }

}
