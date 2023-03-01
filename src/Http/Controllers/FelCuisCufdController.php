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
    
            \Log::debug("GET Company");
            $sectorDocument = SectorDocumentTypes::where('company_id', $company_id)->where('codigo', $sectorDocumentCode)->select('codigoSistema')->first();
    
            if( empty($sectorDocument) ) {
                return response()->json([
                    "success" => false,
                    "msg" => 'No se encontró en Documento Sector #' . $sectorDocumentCode,
                 ], 404);
            }

            $pos_code = $pos_code == 0 ? null : $pos_code;

            $cuis = FelCuis::where('company_id', $company_id)->where('branch_code', $branch_code)->where('system_code', $sectorDocument->codigoSistema)->where('pos_code', $pos_code )->first();

            if( empty($cuis) ) {
                return response()->json([
                    "success" => false,
                    "msg" => 'No se encontro un CUIS',
                 ], 404);
            }

            $cufd = FelCufd::where('company_id', $company_id)->where('branch_code', $branch_code)->where('system_code', $sectorDocument->codigoSistema)->where('pos_code', $pos_code )->first();

            if( empty($cufd) ) {
                return response()->json([
                    "success" => false,
                    "msg" => 'No se encontro un CUFD',
                 ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'cuis' => new CuisResource( $cuis ),
                    'cufd' => new CufdResource( $cufd ),
                ] 
            ]);

        
        } catch( Exception $ex ) {

            \Log::debug($ex->getMessage() . ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine());

            return response()->json([
                "success" => false,
                "msg" => $ex->getMessage(),
             ]);

        }

    }

}
