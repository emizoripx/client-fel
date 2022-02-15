<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Utils\Ninja;
use EmizorIpx\ClientFel\Models\InvoiceMessageWhatsapp;
use Illuminate\Http\Request;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasWhatsappDelivered;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasWhatsappFailed;
use Illuminate\Routing\Controller;
use EmizorIpx\ClientFel\Utils\WhatsappMessageStates;

class WhatsappController extends Controller
{
    

    public function callback(Request $request){

        \Log::debug("WHATSAPP CALLBACK >>>>>>>>>>>>>>>>>>>>>> INICIO");
        
        $data = $request->all();
        
        \Log::debug("WHATSAPP CALLBACK >>>>>>>>>>>>>>>>>>>>>> DATA: " . json_encode($data));

        if( isset($data['statuses']) ){
            $data = $data['statuses'][0];
            $message = InvoiceMessageWhatsapp::where('message_id', $data['message_id'])->first();

            if( ! is_null($message) ){
                \Log::debug("WHATSAPP CALLBACK >>>>>>>>>>>>>>>>> UPDATE MESSAGE ID: " . $message->id );

                $data_update = [
                    "status" => $data['status'],
                    "state" => $data['state'],
                    "status_description" => WhatsappMessageStates::getDescriptionState($data['state']),
                    "error_details" => array_key_exists('details', $data) ? $data['details'] : null
                ];

                $data_update = array_merge($data_update, WhatsappMessageStates::setStateDate($data['state'], isset($data['timestamp']) ? $data['timestamp'] : null ));

                $message->update($data_update);
                
            

            if( $data['state'] == WhatsappMessageStates::DELIVERED){
                event(new InvoiceWasWhatsappDelivered($message->invoice_id, Ninja::eventVars(auth()->user() ? auth()->user()->id : null), $message->number_phone));
            
            } elseif( $data['state'] == WhatsappMessageStates::FAILED ){
                event(new InvoiceWasWhatsappFailed($message->invoice_id, Ninja::eventVars(auth()->user() ? auth()->user()->id : null), $message->number_phone));
            }
                

            } else {
                \Log::debug("WHATSAPP CALLBACK >>>>>>>>>>>>>>>>> MENSAJE NO ENCONTRADO ");
            }

        }

        \Log::debug("WHATSAPP CALLBACK >>>>>>>>>>>>>>>>> FIN");
        return response()->json(['status' => 'success'], 200);

    }

}
