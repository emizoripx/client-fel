<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Http\Resources\BranchResource;
use EmizorIpx\ClientFel\Models\FelBranch;

trait CompanyParametersTrait{

    public function fel_branch(){
        return $this->hasMany(FelBranch::class, 'company_id');
    }

    public function includeBranchData(){
        $branch = $this->fel_branch;

        return is_null($branch) ? null : new BranchResource($branch);
    }

}