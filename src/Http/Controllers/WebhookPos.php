<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\ClientFel\Models\FelPOS;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PostpagoPlanCompany;
use EmizorIpx\PrepagoBags\Repository\AccountPrepagoBagsRepository;
use Illuminate\Http\Request;

class WebhookPos extends BaseController
{
    public function updatePos(Request $request)
    {

        $data = $request->get('data');
        
        \Log::debug("WEBHOOK-POS>>>>>>>>>>>>>>>>>>>>>>>>>>>> INICIO");
        \Log::debug("Data");
        \Log::debug($data);


        // $companies =  AccountPrepagoBags::where('fel_company_id', $data['company_id'])->get();

        $companies = AccountPrepagoBags::where('fel_company_id', $data['company_id'])
                        ->orWhere('company_id', $data['company_id'])
                        ->get();

        if ($companies && $companies->count() > 0) {
            foreach ($companies as $company) {
                
                $branch = FelBranch::where('codigo', $data['branch_code'])->where('company_id', $company->company_id)->first();
                if (!empty($branch)) {
                    // TODO: Validate branch counter and enable overflow

                    $pos = FelPOS::where('company_id', $company->company_id)->where('branch_id', $branch->id)->where('codigo', $data['code'])->first();

                    if($data['closed_at'] != null){
                        \Log::debug("WEBHOOK-POS>>>>>>>>>>>>>>>>>>>>>>>>>>>>  POS delete");
                        $pos->delete();

                        continue;
                    }

                    if(empty($pos)){
                        \Log::debug("WEBHOOK-POS>>>>>>>>>>>>>>>>>>>>>>>>>>>>  POS create");
                        FelPOS::create([
                            'codigo' => $data['code'],
                            'descripcion' => $data['name'],
                            'branch_id' => $branch->id,
                            'company_id' => $company->company_id,
                            'tipoPos' => $data['type_pos'],
                            'numeroContrato' => $data['contract_number'] ?? null,
                            'nitComisionista' => $data['commisionist_nit'] ?? null,
                            'fechaInicio' => $data['from_date'] ?? null,
                            'fechaFin' => $data['to_date'] ?? null,
                        ]);
                        \Log::debug("POS created");
                    } else {
                        \Log::debug("WEBHOOK-POS>>>>>>>>>>>>>>>>>>>>>>>>>>>>  POS to Update");
                        if($pos){
                            $pos->codigo = $data['code'];
                            $pos->descripcion = $data['name'];
                            $pos->branch_id = $branch->id;
                            $pos->company_id = $company->company_id;
                            $pos->tipoPos = $data['type_pos'];
                            $pos->numeroContrato = $data['contract_number'] ?? null;
                            $pos->nitComisionista = $data['commisionist_nit'] ?? null;
                            $pos->fechaInicio = $data['from_date'] ?? null;
                            $pos->fechaFin = $data['to_date'] ?? null;
                            $pos->save();
                            \Log::debug("POS to Updated");
                        }
                    }

                } else {
                    \Log::debug("WEBHOOK-POS>>>>>>>>>>>>>>>>>>>>>>>>>>>> No existe la sucursal");

                }
            }
        } else {
            \Log::debug("WEBHOOK-POS>>>>>>>>>>>>>>>>>>>>>>>>>>>>  No exite la compañia");
        }
    }
}
