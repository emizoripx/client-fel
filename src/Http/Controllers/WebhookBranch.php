<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Illuminate\Http\Request;

class WebhookBranch extends BaseController
{
    public function updateBranch ( Request $request ){

        $data = $request->get('data');
        $new_branch = $request->get('new_branch');
        \Log::debug("Data");
        \Log::debug($data);

        if($new_branch){
            $companies =  AccountPrepagoBags::where('fel_company_id', $data['company_id'])->get();

            if($companies){
                foreach ($companies as $company) {

                    if(!FelBranch::where('codigo', $data['code'])->where('company_id', $company->id)->exists()){
                        
                        FelBranch::create([
                            "codigo" => $data['code'],
                            "descripcion" => $data['code'] == 0 ? "Casa Matriz" : "Sucursal " . $data["code"],
                            "company_id" => $company->id,
                            "zona" => $data['zone'],
                            "pais" => $data['country'],
                            "ciudad" => $data['city'],
                            "municipio" => $data['municipalidad']
                        ]);
                        \Log::debug("Branch Updated");
                        \Log::debug("Company ID: ". $company->id . " Branch #".$data["code"]);
                    }
                }
            } else {
                \Log::debug("No exite la compañia");
            }
        }
        

    }
}
