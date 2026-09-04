<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Request;
use Carbon\Carbon;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;

class WebhookTemplate extends BaseController {

    public function updateTemplates ( Request $request) {

        \Log::debug("WEBHOOK TEMPLATE >>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INIT");
        \Log::debug("WEBHOOK TEMPLATE DATA: " .json_encode($request->get('data')));
        $data = $request->get('data');

        $companies = AccountPrepagoBags::where('fel_company_id', $data['company_id'])
                        ->orWhere('company_id', $data['company_id'])
                        ->get();
        
        $templates = $data['templates'] ?? [];

        $array_templates = [];
        if ($companies->count() > 0 && !empty($templates)) {
            try {
                \Log::debug("WEBHOOK TEMPLATE ITERATING COMPANIES");
                foreach ($companies as $company) {
                    $company_id = $company->company_id;
                    \Log::debug("WEBHOOK TEMPLATE COMPANY : " . $company_id);

                    \DB::table('fel_templates')->where('company_id', $company_id)->delete();

                    $array_parsed = collect($templates)->map(function ($item) use ($company_id) {
                        $arr = (array) $item;

                        $arr_temp = array_merge($arr, [
                            'company_id' => $company_id,
                            'branch_code' => $item['codigoSucursal'] ?? 0,
                            'pos_code' => $item['codigoPuntoVenta'] ?? null,
                            'updated_at' => Carbon::now()->toDateTimeString()
                        ]);
                        unset($arr_temp['codigoSucursal']);
                        unset($arr_temp['codigoPuntoVenta']);

                        return $arr_temp;
                    })->all();

                    $array_templates = array_merge($array_templates, $array_parsed);
                    \Log::debug("Updated templates company ID: " . $company_id);
                }

                if (!empty($array_templates)) {
                    \DB::table('fel_templates')->upsert($array_templates, ['document_sector_code', 'company_id', 'branch_code', 'pos_code'], ['display_name', 'blade_resource', 'pos_code', 'updated_at']);
                }
            } catch (\Throwable $th) {
                \Log::debug("errors in update templates " . $th->getMessage());
            }
        }
       
        return response()->json(['status' => true], 200);
    }
}
