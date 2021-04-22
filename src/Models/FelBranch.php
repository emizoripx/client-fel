<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class FelBranch extends Model
{

    protected $table = 'fel_branches';

    protected $guarded = [];

    public static function existsBranch($company_id, $branch_code){
        $branch = self::where('company_id', $company_id)->where('codigo', $branch_code)->first();

        return is_null($branch);
    }


    // public function fel_pos(){
    //     return $this->hasMany();
    // }

}
