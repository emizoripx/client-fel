<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PartnerConfiguration;
use Exception;
use Illuminate\Http\Request;

class WebhookDocumentSector extends BaseController {

    public function updateDocumentSector ( Request $request ) {

        \Log::debug("WEBHOOK DOCUMENT SECTOR >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INIT");

        try {

            $data = $request->get('data');
    
            $companies = AccountPrepagoBags::where('fel_company_id', $data['company_id'])
                            ->orWhere('company_id', $data['company_id'])
                            ->get();
    
            if ($companies->count() > 0) {
                foreach ($companies as $companyAccount) {
                    $company = $companyAccount->company;
                    $isPartner = !empty($company->settings->is_b2b2b_partner);
                    
                    if ($isPartner) {
                        $phase = ($companyAccount->phase === 'Production') ? 'production' : 'testing';
                        $partnerConfig = PartnerConfiguration::getConfig($phase);
                        $host = rtrim($partnerConfig['api_url'] ?? '', '/');
                        $token = $partnerConfig['partner_token'] ?? '';
                        $tenantKey = $company->settings->tenant_id ?? ($company->settings->id_number ?? $company->id);

                        $parametric_service = new Parametric($token, $host, $tenantKey);
                    } else {
                        $token = $companyAccount->fel_company_token ? $companyAccount->fel_company_token->getAccessToken() : null;
                        $host = $companyAccount->fel_company_token ? $companyAccount->fel_company_token->getHost() : ($data['host'] ?? config('clientfel.host_demo'));
                        $parametric_service = new Parametric($token, $host);
                    }

                    $parametric_service->get(TypeParametrics::TIPOS_DOCUMENTO_SECTOR, '', true);
                    $document_response = $parametric_service->getResponse();

                    if (!empty($document_response) && is_array($document_response)) {
                        $document_sector_uniques = collect($document_response)->unique('codigoDocumentSector')->values()->all();
                        $doc_sector_code = collect($document_sector_uniques)->pluck('codigoDocumentSector')->all();

                        \DB::table('fel_sector_document_types')->where('company_id', $companyAccount->company_id)->whereNotIn('codigo', $doc_sector_code)->delete();
                        FelParametric::saveParametrics(TypeParametrics::TIPOS_DOCUMENTO_SECTOR, $companyAccount->company_id, $document_response, true);
                    }

                    $companyAccount->service()->registerCompanySectorDocuments();
                    \Log::debug("WEBHOOK DOCUMENT SECTOR ------ Actualizado Company ID " . $companyAccount->company_id);
                }
            }
        } catch( Exception $ex ) {
            \Log::debug("WEBHOOK DOCUMENT SECTOR - Error al Actualizar documento sector : " . $ex->getMessage());
        }

        return response()->json(['status' => true], 200);
    }
}