<?php

namespace EmizorIpx\ClientFel\Services\OfflinePackage;

use EmizorIpx\ClientFel\Models\FelOfflinePackage;
use Exception;

class OfflinePackageService {

    public function getOrCreateOfflinePackage( $company_id, $branch_id, $pos_id, $type_document_code, $offline_event_id ){

        \Log::debug("Get Or Create Offline package");
        $offline_package = FelOfflinePackage::where('company_id', $company_id)
                            ->where('branch_id', $branch_id)
                            ->where('pos_id', $pos_id)
                            ->where('type_document_code', $type_document_code)
                            ->where('offline_event_id', $offline_event_id)
                            ->where('state', FelOfflinePackage::PENDING_STATE)
                            ->first();
                            
        if( !$offline_package ) {
            \Log::debug("Create Offline Package: ");
            try {
            $offline_package = FelOfflinePackage::create([
                'company_id' => $company_id,
                'branch_id' => $branch_id,
                'pos_id' => $pos_id,
                'type_document_code' => $type_document_code,
                'offline_event_id' => $offline_event_id,
                'state' => FelOfflinePackage::PENDING_STATE
            ]);
            }catch(Exception $ex) {
                \Log::debug("Error to create package: " . $ex->getMessage());
            }
            \Log::debug("Created  Offline Package: " . json_encode($offline_package));

        }

        \Log::debug("Offline Package GET: " . json_encode($offline_package));

        return $offline_package;

    }

}