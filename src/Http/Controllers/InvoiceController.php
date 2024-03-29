<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Utils\Ninja;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasEmited;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasEmitedUpdate;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use Exception;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{

    public function emit(Request $request)
    {
        \Log::debug("################################################## FLUJO ============================================ INICIA EMISION DE FACTURA");
        \Log::debug("EMIT-FROM-PREFACTURA >>>>>>>>>>>>>>>>>");
        $success = false;

        try {

            $felInvoiceRequest = FelInvoiceRequest::findByIdOrigin($request->input('id_origin'));

            if(!$felInvoiceRequest){
                return response()->json([
                    "success" => false,
                    "msg" => "Factura no encontrada para emitir"
                ]);
            }
            if($felInvoiceRequest->codigoEstado == 690){
                throw new ClientFelException('La factura ya fue emitida');
            }
            $invoice = $felInvoiceRequest->invoice_origin();
            \Log::debug("EMIT-INVOICE ==============> START TRANSACTION");
            // begin a trasaction in case an error happend, rollback changes
            // save number in felinvoicerequest 

            // generate next number new emission invoice
            if ($invoice->number == 0) {
                \Log::debug("\n\n\n\n\n ASIGNANDO VALOR desde PREFACTURA EMIT =================invoice_number is set up cause number is not assigned \n\n\n\n\n\n");
                // generate next number new emission invoice
                $invoice->service()->applyNumber()->save();
            } else {
                \Log::debug(" \n\n\n\n =============Number is assigned  " . $invoice->number . " \n\n\n\n\n\n");
            }

            $felInvoiceRequest->setNumeroFactura($invoice->number);
            // reload changes in model
            $felInvoiceRequest = $felInvoiceRequest->fresh();
            $felInvoiceRequest->setAccessToken()->sendInvoiceToFel();
            // $felInvoiceRequest->invoiceDateUpdatedAt();
            $felInvoiceRequest->deletePdf();
            $invoice->service()->markSent()->save();

            \Log::debug("Update user assigned " . auth()->user()->id );
            $invoice->assigned_user_id = auth()->user()->id;
            $invoice->save();
            $felInvoiceRequest->setEmittedByUser();
            $success = true;
            
            \Log::debug("EMIT-INVOICE ==============> END TRANSACTION");
            fel_register_historial($felInvoiceRequest);

            event(new InvoiceWasEmited($felInvoiceRequest->invoice_origin(), $felInvoiceRequest->invoice_origin()->company, Ninja::eventVars(auth()->user()->id) ));
            \Log::debug("################################################## FLUJO ============================================ FACTURA EMITIDA");
            // return redirect('api/v1/invoices/'. $request->input('id_origin'));
            return response()->json([
                'success' => true
            ]);

        } catch (ClientFelException $ex) {
            
            fel_register_historial($felInvoiceRequest, $ex->getMessage());
            // return redirect('api/v1/invoices/' . $request->input('id_origin'));
            return response()->json([
                "success" => false,
                "msg" => $ex->getMessage()
            ]);
        }
    }

    public function revocate(Request $request){
        try{
            $felInvoiceRequest = FelInvoiceRequest::findByIdOrigin($request->input('id_origin'));

            if(!$felInvoiceRequest){
                throw new ClientFelException("Factura no encontrada");
            }

            if(!is_null($felInvoiceRequest->getRevocationReasonCode())){
                throw new ClientFelException("La Factura ya fue anulada");
            }

            $felInvoiceRequest->setAccessToken()->sendRevocateInvoiceToFel($request->input('codigo_motivo_anulacion'));
            $felInvoiceRequest->invoiceDateUpdatedAt();
            $felInvoiceRequest->setRevocatedByUser();
            return response()->json([
                'success' => true
            ]);
            

        } catch(ClientFelException $ex){
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);

        }
    } 
    public function reversionRevocate(Request $request){
        try{
            $felInvoiceRequest = FelInvoiceRequest::findByIdOrigin($request->input('id_origin'));

            if(!$felInvoiceRequest){
                throw new ClientFelException("Factura no encontrada");
            }

            if($felInvoiceRequest->codigoEstado != 691){
                throw new ClientFelException("La Factura debe de estar anulada");
            }

            $felInvoiceRequest->setAccessToken()->sendRevocateReversionInvoiceToFel();
            $felInvoiceRequest->invoiceDateUpdatedAt();
            return response()->json([
                'success' => true
            ]);
            

        } catch(ClientFelException $ex){
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);

        }
    }
    public function getStatus(Request $request){
        try{
            $felInvoiceRequest = FelInvoiceRequest::findByIdOrigin($request->input('id_origin'));

            if(!$felInvoiceRequest){
                throw new ClientFelException("Factura no encontrada");
            }
            $response = $felInvoiceRequest->setAccessToken()->sendVerifyStatus();

            $felInvoiceRequest->invoiceDateUpdatedAt();

            return response()->json($response);

        } catch(ClientFelException $ex){
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);
        }
    }

    public function updateEmitedInvoice(Request $request){
        try {
            $felInvoiceRequest = FelInvoiceRequest::findByIdOrigin($request->input('id_origin'));

            if(!$felInvoiceRequest){
                throw new Exception(json_encode(["errors" => ["La factura no fue encontrada"]]));
            }
            if($felInvoiceRequest->codigoEstado == 690){
                throw new ClientFelException('La factura ya fue emitida');
            }

            $felInvoiceRequest->setAccessToken()->sendUpdateInvoiceToFel();

            $felInvoiceRequest->invoiceDateUpdatedAt();

            $felInvoiceRequest->deletePdf();

            event(new InvoiceWasEmitedUpdate($felInvoiceRequest->invoice_origin(), $felInvoiceRequest->invoice_origin()->company, Ninja::eventVars(auth()->user()->id)));

            fel_register_historial($felInvoiceRequest);

            return response()->json([
                'success' => true
            ]);

        } catch (Exception $ex) {
            \Log::debug('Errors ' . $ex->getMessage());
            fel_register_historial(isset($felInvoiceRequest) ? $felInvoiceRequest : null, json_decode($ex->getMessage()));

            return response()->json([
                'success' => false,
                'msg' => json_decode($ex->getMessage())
            ]);
        }
    }

    public function verifynit(Request $request,$nit)
    {
        info("ingresando aca a validar el nit  " . $request->company_name);
        if ( !is_numeric($nit) ) {
            return response()->json([
                "success" => false,
                "message" => "NIT INEXISTENTE",
                "codigo" => 994
            ]);
        }

        $success = false;
        try {
            $invoice_service = new Invoices($request->host, $request->access_token);
            $invoice_service->validateNit($nit);

            if ($invoice_service->isSuccessful()) {
                $response = $invoice_service->getResponse();
                \Log::debug($response);


                if ($response['codigo'] == 986) {
                    $success = true;
                }

                return response()->json([
                    "success" => $success,
                    "message" => $response['descripcion'],
                    "codigo" => $response['codigo']
                ]);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => $invoice_service->getErrors()[0]['description'],
                    "codigo" => 994
                ]);
            }
           
        } catch (ClientFelException $ex) {
            return response()->json([
               "success" => false,
                "message" => $ex->getMessage(),
                "codigo" => -1
            ]);
        }

    }
}
