<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Models\ClientContact;
use EmizorIpx\ClientFel\Exceptions\WhatsappException;
use EmizorIpx\ClientFel\Models\InvoiceMessageWhatsapp;
use EmizorIpx\ClientFel\Services\Whatsapp\Whatsapp;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Utils\WhatsappMessageStates;
use Exception;

trait InvoiceSendMessageWhatsappTrait {

    public function sendMessageWhatsapp(){

        \Log::debug("Contact Send >>>>>>>>>>>>>>>>>>>>> ");
        $contact_key = request('contact_key');
        $phone_number = request('numero_telefono');

        $client_contact = ClientContact::where('contact_key', $contact_key)->first();

        \Log::debug("Contact >>>>>>>>>>>>>>>>>>>>> " . json_encode($client_contact));

        try {

            $invoice_message_whatsapp = InvoiceMessageWhatsapp::create([
                'company_id' => $client_contact->company_id,
                'client_contact_id' => $client_contact->id,
                'invoice_id' => $this->id,
                'user_id' => auth()->user()->id
            ]);


            \Log::debug(">>>>>>>>>>>>>>>>>>>>>>>>>> " . $phone_number);
            if( ! isset($phone_number)){
                if($client_contact->phone != null){
                    \Log::debug(">>>>>>>>>>>>>>>>>>>>>>>>>> ");
                    $phone_number = $client_contact->phone;

                } else {
                    \Log::debug("No se encontró un número de teléfono");
                    throw new WhatsappException( json_encode(["errors" => ["No se encontró un número de teléfono"]]));
                }
            }
    
            \Log::debug(">>>>>>>>>>>>>>>>>>>>>>>>>> create service " );
            $whatsapp_service = new Whatsapp();

            \Log::debug(">>>>>>>>>>>>>>>>>>>>>>>>>> created service " );
    
            $whatsapp_service->setNumber($phone_number);
            $invoice_message_whatsapp->update([
                'number_phone' => $phone_number
            ]);

            [$is_authorize, $failedToOptInNumbers ] = $whatsapp_service->authorizationOfSending();

            if($is_authorize){

                $fel_invoice = $this->fel_invoice;
                $company = $this->company;

                $pdf_url = $this->pdf_file_path();

                \Log::debug("PDF URL >>>>>>>>>>>>>>>>>> " . $pdf_url);

                $data = [
                    "nit" => $fel_invoice->complemento == null ? $fel_invoice->numeroDocumento : $fel_invoice->numeroDocumento . ' ' . $fel_invoice->complemento,
                    "company_name" => $company->settings->name,
                    "monto_total" => $fel_invoice->montoTotal,
                    "contact_key" => $client_contact->contact_key,
                    "pdf_name" => "Factura". $fel_invoice->numeroFactura . ".pdf",
                    "pdf_url" => $pdf_url
                ];

                $whatsapp_service->setData($data);

                $response = $whatsapp_service->sendMessage();

                $status_response = $response['statuses'][0];

                $invoice_message_whatsapp->update([
                    'status' => $status_response['status'],
                    'state' => $status_response['state'],
                    'message_id' => $status_response['message_id'],
                    'send_date' => Carbon::now()->toDateTimeString(),
                    'message' => json_encode($data)
                ]);

                if( $status_response['status'] == 'failure' ){
                    \Log::debug("Error al enviar el Mensaje");
                    throw new WhatsappException( json_encode(["errors" => ['Error al enviar el Mensaje']]));
                }

                $msg = WhatsappMessageStates::getDescriptionState($status_response['state']);

                $invoice_message_whatsapp->update([
                    'status_description' => $msg
                ]);

                // $invoice_message_whatsapp->update($whatsapp_array_data);

                return $msg;


            } else {

                $invoice_message_whatsapp->update([
                    'authorize_to_sent' => false,
                    'rejection_reason' => $failedToOptInNumbers['rejectionReason']
                ] );

                \Log::debug("Autorización de envío Denegada, Número:");

                throw new WhatsappException( json_encode(["errors" => ["Autorización de envío Denegada, Número: " . $failedToOptInNumbers[0]['msisdn'] . " Razón: " . $failedToOptInNumbers[0]['rejectionReason']]]));
            }
            
    
        } catch (WhatsappException $ex){

            $invoice_message_whatsapp->update([
                'errors' => $ex->getMessage()
            ] );

            
            \Log::debug($ex->getMessage());

            throw new Exception($ex->getMessage());
        }
    }

}