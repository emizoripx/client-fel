<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use EmizorIpx\ClientFel\Jobs\ProcessOfflineInvoices;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelOfflineEvent;
use EmizorIpx\ClientFel\Models\FelOfflinePackage;
use EmizorIpx\ClientFel\Services\OfflineEvent\FelOfflineEventService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FelOfflineEventController extends Controller
{
    
    

    public function processEvent( Request $request,  $event_id ){

        try {

            $company_id = auth()->user()->getCompany()->id;
    
            ProcessOfflineInvoices::dispatch($event_id, $company_id);
    
            return response()->json([
                "success" => true,
                "message" => "Evento $event_id Enviado a Procesar"
            ]);
        } catch( Exception $ex ) {
            \Log::debug("Ocurrio un error al enviar a processar el evento: " . $event_id . " Error: " . $ex->getMessage());

            return response()->json([
                "success" => false,
                "message" => $ex->getMessage()
            ]);
        }

    }

    public function closeEvent ( Request $request, $event_id ) {

        try {

            $company = auth()->user()->getCompany();
    
            
            $offline_event = FelOfflineEvent::where('company_id', $company->id)->where('id', $event_id)->where('state', FelOfflineEvent::PROCESSED_STATE)->first();

            \Log::debug("Offline Event : " . json_encode($offline_event));
            if( ! $offline_event ) {
                throw new Exception(" No se encontro el evento ");
            }

            if( ! is_null( $offline_event->errors ) ) {
                throw new Exception(' El evento tiene errores:  ' . $offline_event->errors);
            }

            $offline_packages = FelOfflinePackage::where('company_id', $company->id)->where('offline_event_id', $offline_event->id)->where( function ( $query ) {
                $query->where('state', FelOfflinePackage::PENDING_STATE)->orWhere('has_errors', true);
            })->get();


            \Log::debug("Offline Packages Errors:  " . json_encode($offline_packages));
            // Set packages with errors to Failed state

            if( !empty( $offline_packages ) && count($offline_packages) > 0 ){
                return response()->json([
                    "success" => false,
                    "message" => "Existen Facturas con errores"
                ]);
            }

            $company_token = FelClientToken::where('account_id', $company->id)->firstOrFail();

            if( ! $company_token ) {
                \Log::debug("No se encontro credenciales para la compañía: " . $company->id);
                throw new Exception("No se encontro credenciales para la compañía");
            }

            $fel_offline_event_service = new FelOfflineEventService( $company_token->host );

            $fel_offline_event_service->setAccessToken( $company_token->access_token );

            $fel_offline_event_service->setSignificantEventCode( $offline_event->fel_significant_event_id );

            $response = $fel_offline_event_service->closeSignificantEvent();

            \Log::debug("Response Close Event: " . json_encode($response));

            $message = '';

            if( isset($response['status']) && $response['status'] != 'success' ) {

                $offline_event->fel_errors = $response['errors'];
                $offline_event->save();

                $message = $response['errors']['messages'];

            } else {

                $offline_event->fel_response = $response['data'];
                $offline_event->save();

                $message = $response['data']['messages'];
            }

            return response()->json([   
                'success' => true,
                'message' => $message
            ]);

        } catch ( Exception $ex ) {

            \Log::debug("Error al cerrar el evento: " . $ex->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar el evento: ' . $ex->getMessage()
            ]);

        }


    }

}
