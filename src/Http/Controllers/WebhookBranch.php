<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PostpagoPlanCompany;
use EmizorIpx\PrepagoBags\Repository\AccountPrepagoBagsRepository;
use Illuminate\Http\Request;

class WebhookBranch extends BaseController
{
    public function updateBranch(Request $request)
    {

        $data = $request->get('data');
        
        \Log::debug("WEBHOOK-BRANCH>>>>>>>>>>>>>>>>>>>>>>>>>>>> INICIO");
        \Log::debug("Data");
        \Log::debug($data);


        $companies =  AccountPrepagoBags::where('fel_company_id', $data['company_id'])->get();

        if ($companies) {
            foreach ($companies as $company) {
                
                $branch = FelBranch::where('codigo', $data['code'])->where('company_id', $company->company_id)->first();
                if (empty($branch)) {
                    // TODO: Validate branch counter and enable overflow
                    $postpago_plan = PostpagoPlanCompany::where('company_id', $company->company_id)->first();
                    $counter_branches = AccountPrepagoBagsRepository::getCounterBranches($company->company_id);
                    if( $postpago_plan && $company->is_postpago && $postpago_plan->service()->verifyLimitBranches($counter_branches) && !$postpago_plan->enable_overflow){

                        \Log::debug("LLego al limite de Sucursales");
                        bitacora_info("Webhook:CreateBranch", 'Ya llego al limite de suscursales company: ' . $company->company_id);

                        continue;
                    }
                        FelBranch::create([
                            "codigo" => $data['code'],
                            "descripcion" => $data['code'] == 0 ? "Casa Matriz" : "Sucursal " . $data["code"],
                            "company_id" => $company->company_id,
                            "zona" => $data['zone'],
                            "telefono" => $data['phone'],
                            "pais" => $data['country'],
                            "ciudad" => $data['city'],
                            "municipio" => $data['municipalidad']
                        ]);

                        AccountPrepagoBagsRepository::updateCounterBranches($company->company_id);

                        \Log::debug("Branch created");

                        // sync sector document type
                        $fel_sector_document_types = \DB::table('fel_sector_document_types')->whereCompanyId($company->company_id)->where('codigoSucursal', 0)->get();
                        foreach ($fel_sector_document_types as $fel_sector_document_type) {
                            
                            \DB::table('fel_sector_document_types')->insert([
                                "company_id" => $fel_sector_document_type->company_id,
                                "codigoSucursal" => $data['code'],
                                "documentoSector" => $fel_sector_document_type->documentoSector,
                                "tipoFactura" => $fel_sector_document_type->tipoFactura,
                                "codigo" => $fel_sector_document_type->codigo
                            ]);
                        }
                        \Log::debug("fel_sector_document_types assigned");
                       
                        \Log::debug("Company ID: " . $company->company_id . " Branch #" . $data["code"]);
                } else {
                    \Log::debug("Branch Updated");

                    if($branch){
                        $branch->codigo = $data['code'];
                        $branch->descripcion = $data['code'] == 0 ? "Casa Matriz" : "Sucursal " . $data["code"];
                        $branch->company_id = $company->company_id;
                        $branch->zona = $data['zone'];
                        $branch->telefono = $data['phone'];
                        $branch->pais = $data['country'];
                        $branch->ciudad = $data['city'];
                        $branch->municipio = $data['municipalidad'];
                        $branch->save();

                        \Log::debug("Se actualizó la Surcursal #" . $data['code']);
                    }
                }
            }
        } else {
            \Log::debug("No exite la compañia");
        }
    }
}
