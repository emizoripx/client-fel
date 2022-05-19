<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Exception;
use Illuminate\Http\Request;

class WebhookDocumentSector extends BaseController {

    public function updateDocumentSector ( Request $request ) {

        \Log::debug("WEBHOOK DOCUMENT SECTOR >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INIT");

        try {

            $data = $request->get('data');
    
            $companies = AccountPrepagoBags::join('fel_company_tokens', 'fel_company.company_id', 'fel_company_tokens.account_id')
                            ->where('fel_company.fel_company_id', $data['company_id'])
                            ->where('fel_company_tokens.host', $data['host'])
                            ->select( 'fel_company.id', 'fel_company.company_id', 'fel_company.fel_company_id', 'fel_company_tokens.access_token', 'fel_company_tokens.host')
                            ->get();
    
            $company_first = $companies->first();
    
            $parametric_service = new Parametric( $company_first->access_token, $company_first->host );
    
            $document_response = $parametric_service->get( TypeParametrics::TIPOS_DOCUMENTO_SECTOR );
    
            $document_sector_uniques = collect( $document_response )->unique('codigoDocumentSector')->values()->all();
    
            $doc_sector_code = collect( $document_sector_uniques )->pluck('codigoDocumentSector')->all();
    
            \Log::debug("Doc Sector Codes:  ", [ $doc_sector_code ]);
    
            if( sizeof($companies) > 0  ) {
    
                foreach ($companies as $company) {
                    
                    \DB::table('fel_sector_document_types')->where('company_id', $company->company_id)->whereNotIn('codigo', $doc_sector_code)->delete();
    
                    \Log::debug("WEBHOOK DOCUMENT SECTOR ------Actualizar Company ID " . $company->company_id);
                    FelParametric::saveParametrics( TypeParametrics::TIPOS_DOCUMENTO_SECTOR, $company->company_id, $document_response);
    
                    $company->service()->registerCompanySectorDocuments();
    
                }
    
            }
        } catch( Exception $ex ) {

            \Log::debug("WEBHOOK DOCUMENT SECTOR - Error al Actulizar documento sector : " . $ex->getMessage());

        }

        return response()->json(['status' => true], 200);



    }


}