<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Request;
use Carbon\Carbon;

use function PHPSTORM_META\type;

class WebhookTemplate extends BaseController {

    public function updateTemplates ( Request $request) {

        \Log::debug("WEBHOOK TEMPLATE >>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INIT");
        \Log::debug("WEBHOOK TEMPLATE DATA: " .json_encode($request->get('data')));
        $data = $request->get('data');

        $companies = \DB::table('fel_company')
                        ->join('fel_company_tokens', 'fel_company.company_id', 'fel_company_tokens.account_id')
                        ->where('fel_company.fel_company_id', $data['company_id'])
                        ->where('fel_company_tokens.host', $data['host'])
                        ->select('fel_company.company_id', 'fel_company.fel_company_id')
                        ->get();
        
        $templates = $data['templates'];

        // \Log::debug("Companies: " . json_encode($companies));

        $array_templates = [];
        if( $companies ) {
            \Log::debug("WEBHOOK TEMPLATE ITERATING COMPANIES");
            foreach ($companies as $company) {
                $company_id = $company->company_id;
                \Log::debug("WEBHOOK TEMPLATE COMPANY : ".$company_id);
                $array_parsed = collect( $templates )->map( function( $item ) use ($company_id) {

                    $arr = (array) $item;

                    $arr_temp = array_merge($arr, [ 'company_id' =>  $company_id, 'branch_code' => $item['codigoSucursal'], 'updated_at' => Carbon::now()->toDateTimeString() ]);
                    unset($arr_temp['codigoSucursal']);

                    return $arr_temp;


                })->all();

                $array_templates = array_merge($array_templates, $array_parsed);

                \Log::debug("Upated templates company ID: " . $company->company_id);

            }

        }

        \DB::table('fel_templates')->upsert($array_templates, ['document_sector_code', 'company_id', 'branch_code'], ['display_name', 'blade_resource', 'updated_at']);

        return response()->json(['status' => true], 200);

    }

}