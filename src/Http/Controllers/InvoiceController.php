<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Utils\Ninja;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasEmited;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasEmitedUpdate;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use Exception;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{

    public function emit(Request $request)
    {

        
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
            
            $felInvoiceRequest->setAccessToken()->sendInvoiceToFel();

            $felInvoiceRequest->invoiceDateUpdatedAt();
            
            $felInvoiceRequest->deletePdf();


            $success = true;

            fel_register_historial($felInvoiceRequest);

            event(new InvoiceWasEmited($felInvoiceRequest->invoice_origin(), $felInvoiceRequest->invoice_origin()->company, Ninja::eventVars(auth()->user()->id) ));

            return response()->json([
                "success" => $success
            ]);

        } catch (ClientFelException $ex) {
            
            fel_register_historial($felInvoiceRequest, $ex->getMessage());

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
            \Log::debug('Errors');
            fel_register_historial(isset($felInvoiceRequest) ? $felInvoiceRequest : null, json_decode($ex->getMessage()));

            return response()->json([
                'success' => false,
                'msg' => json_decode($ex->getMessage())
            ]);
        }
    }
}
