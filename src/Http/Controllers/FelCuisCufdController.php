<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use EmizorIpx\ClientFel\Http\Resources\CufdResource;
use EmizorIpx\ClientFel\Http\Resources\CuisResource;
use EmizorIpx\ClientFel\Models\FelCufd;
use EmizorIpx\ClientFel\Models\FelCuis;
use EmizorIpx\ClientFel\Models\Parametric\SectorDocumentTypes;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FelCuisCufdController extends Controller
{
    
    public function getCuisCufd ( Request $request, $sectorDocumentCode, $branch_code, $pos_code ) {

        try {

            $company_id = auth()->user()->getCompany()->id;
    
            $sectorDocument = SectorDocumentTypes::where('company', $company_id)->where('codigo', $sectorDocumentCode)->select('codigoSistema')->first();
    
            if( empty($sectorDocument) ) {
                throw new Exception('No se encontró en Documento Sector #' . $sectorDocumentCode);
            }

            $pos_code = $pos_code == 0 ? null : $pos_code;

            $cuis = FelCuis::where('company_id', $company_id)->where('branch_code', $branch_code)->where('system_code', $sectorDocument->codigoSistema)->where('pos_code', $pos_code )->first();

            if( empty($cuis) ) {
                throw new Exception('No se encontro un CUIS');
            }

            $cufd = FelCufd::where('company_id', $company_id)->where('branc_code', $branch_code)->where('system_code', $sectorDocument->codigoSistema)->where('pos_code', $pos_code )->first();

            if( empty($cufd) ) {
                throw new Exception('No se encontro un CUFD');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'cuis' => new CuisResource( $cuis ),
                    'cufd' => new CufdResource( $cufd ),
                ] 
            ]);

        
        } catch( Exception $ex ) {

            \Log::debug($ex->getMessage());

            return response()->json([
                "success" => false,
                "msg" => $ex->getMessage(),
             ]);

        }

    }

}
