<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Request;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\ClientFel\Models\FelPOS;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Carbon\Carbon;
use Exception;

class WebhookCuis extends BaseController {

    public function updateCuis ( Request $request ) {

        \Log::debug("WEBHOOK CUIS >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INIT");
        
        $data = $request->get('data');
        \Log::debug("WEBHOOK CUIS >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Data: " . json_encode($data));

            $companies = AccountPrepagoBags::join('fel_company_tokens', 'fel_company.company_id', 'fel_company_tokens.account_id')
                            ->where('fel_company.fel_company_id', $data['company_id'])
                            ->where('fel_company_tokens.host', $data['host'])
                            ->select( 'fel_company.id', 'fel_company.company_id', 'fel_company.fel_company_id', 'fel_company_tokens.access_token', 'fel_company_tokens.host')
                            ->get();
            \Log::debug("GET Companies: " . json_encode($companies));

            if( sizeof( $companies ) > 0 ) {
                $cuis = $data['cuis'];

                foreach ( $companies as $company ) {
                    try {

                        $branch = FelBranch::where('company_id', $company->company_id)->where('codigo', $data['codigoSucursal'])->first();

                        if( empty( $branch ) ) {
                            \Log::debug("No se encontró la Sucursal #" . $data['codigoSucursal']);
                            continue;
                        }

                        $pos = null;

                        if( $data['codigoPuntoVenta'] != 0 ) {

                            $pos = FelPOS::where('company_id', $company->company_id)->where('branch_id', $branch->id)->where('codigo', $data['codigoPuntoVenta'])->first();
                        }

                        $query = \DB::table('fel_cuis')->where( 'company_id', $company->company_id )->where( 'branch_id', $branch->id );

                        $query = is_null($pos) ? $query->whereNull('pos_id') : $query->where('pod_id', $pos->id);

                        if( $query->where('validity', $cuis['vigencia'])->exists() ) {
                            \Log::debug("Se encontró un CUIS registrado con los mismos datos ");
                            continue;
                        }

                        \DB::table('fel_cuis')->insert([
                            'cuis' => $cuis['cuis'],
                            'validity' => $cuis['vigencia'],
                            'system_code' => $cuis['codigoSistema'],
                            'company_id' => $company->company_id,
                            'branch_id' => $branch->id,
                            'branch_code' => $branch->codigo,
                            'pos_id' => is_null($pos) ? null : $pos->id,
                            'pos_code' => is_null($pos) ? null : $pos->codigo,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ]);

                        \Log::debug("Added CUIS: " . $cuis['cuis'] . ' Company ID: ' . $company->company_id . ' Branch Code: ' . $branch->codigo  . ' POS code ' . $data['codigoPuntoVenta']);
                    
                    } catch(Exception $ex) {

                        \Log::debug("WEBHOOK CUIS >>>>>>>>>>>>>>>>> Error al al actualizar el CUIS : " . $ex->getMessage());
                
                    }
                }

            } else {
                \Log::debug("No se encontro compañías con para atualizar CUIS ");
            }

    }

}