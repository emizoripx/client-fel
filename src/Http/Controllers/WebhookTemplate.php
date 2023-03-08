<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Request;
use Carbon\Carbon;

class WebhookTemplate extends BaseController {

    public function updateTemplates ( Request $request) {

        \Log::debug("WEBHOOK TEMPLATE >>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INIT");
        \Log::debug("WEBHOOK TEMPLATE DATA: " .json_encode($request->get('data')));
        $data = $request->get('data');
        // \Log::debug("WEBHOOK TEMPLATE DATA PARAMETERS : ",[$data['company_id'], $data['host'], $data['templates']]);

        $companies = \DB::table('fel_company')
                        ->join('fel_company_tokens','fel_company_tokens.account_id', '=', 'fel_company.company_id')
                        ->where('fel_company.fel_company_id', $data['company_id'])
                        ->where('fel_company_tokens.host', $data['host'])
                        ->select('fel_company.company_id', 'fel_company.fel_company_id')
                        ->get();
        
        $templates = $data['templates'];

        // \Log::debug("Companies: " . json_encode($companies));

        $array_templates = [];
        if( sizeof($companies) > 0 ) {

            try{

                $doc_sector_codes = collect( $templates )->pluck('document_sector_code')->all();

                $branch_codes = collect( $templates )->pluck('codigoSucursal')->all();

                $pos_codes = collect( $templates )->pluck('codigoPuntoVenta')->all();

                \Log::debug("Doc sector ", [$doc_sector_codes]);
                \Log::debug("Branch sector ", [$branch_codes]);
                \Log::debug("Pos Codes", [$pos_codes]); 

                \Log::debug("WEBHOOK TEMPLATE ITERATING COMPANIES");
                foreach ($companies as $company) {
                    $company_id = $company->company_id;
                    \Log::debug("WEBHOOK TEMPLATE COMPANY : " . $company_id);
                    // \Log::debug("WEBHOOK TEMPLATE COMPANY, templates : " , $templates);

                    \DB::table('fel_templates')->where('company_id', $company_id)->delete();

                    $array_parsed = collect( $templates )->map( function( $item ) use ($company_id) {

                        $arr = (array) $item;

                        $arr_temp = array_merge($arr, [ 'company_id' =>  $company_id, 'branch_code' => $item['codigoSucursal'], 'pos_code' => $item['codigoPuntoVenta'], 'updated_at' => Carbon::now()->toDateTimeString() ]);
                        unset($arr_temp['codigoSucursal']);
                        unset($arr_temp['codigoPuntoVenta']);

                        return $arr_temp;
                    })->all();

                    $array_templates = array_merge($array_templates, $array_parsed);

                    \Log::debug("Upated templates company ID: " . $company->company_id);

                }


                \DB::table('fel_templates')->upsert($array_templates, ['document_sector_code', 'company_id', 'branch_code', 'pos_code'], ['display_name', 'blade_resource', 'pos_code', 'updated_at']);

            } catch (\Throwable $th) {
                \Log::debug("errors in update templates " . $th->getMessage());
            }
        }
       
        return response()->json(['status' => true], 200);

    }

}
