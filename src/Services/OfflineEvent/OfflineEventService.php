<?php

namespace EmizorIpx\ClientFel\Services\OfflineEvent;

use EmizorIpx\ClientFel\Models\FelOfflineEvent;
use Carbon\Carbon;
use Exception;

class OfflineEventService {

    public function getOrCreateOfflineEvent( $company_id, $branch_id, $pos_id, $cufd, $cuis, $emission_date ){

        \Log::debug("Get Or Create Offline Event");
        $offline_event = FelOfflineEvent::where('company_id', $company_id)
                            ->where('branch_id', $branch_id)
                            ->where('pos_id', $pos_id)
                            ->where('cufd', $cufd)
                            ->where('cuis', $cuis)
                            ->where('state', FelOfflineEvent::PENDING_STATE)
                            ->whereNull('end_date')
                            ->first();
                            
        if( !$offline_event ) {
            \Log::debug("Create Offline Event: ");
            try {
            $offline_event = FelOfflineEvent::create([
                'company_id' => $company_id,
                'branch_id' => $branch_id,
                'pos_id' => $pos_id,
                'cufd' => $cufd,
                'cuis' => $cuis,
                'start_date' => Carbon::parse($emission_date)->toDateTimeString(),
                'state' => FelOfflineEvent::PENDING_STATE
            ]);
            }catch(Exception $ex) {
                \Log::debug("Error to create event: " . $ex->getMessage());
            }
            \Log::debug("Created  Offline Event: " . json_encode($offline_event));

        }

        \Log::debug("Offline Event GET: " . json_encode($offline_event));

        return $offline_event;

    }

}