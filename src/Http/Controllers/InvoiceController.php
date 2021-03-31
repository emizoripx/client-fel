<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Jobs\Util\UnlinkFile;
use App\Models\Invoice;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelInvoiceStatusHistorial;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Utils\TypeDocuments;
use Exception;
use Hashids\Hashids;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{

    public function emit(Request $request)
    {

        
        $success = false;

        // $access_token = FelClientToken::getTokenByAccount($request->company_id);

        try {

            $felInvoiceRequest = FelInvoiceRequest::findByIdOrigin($request->input('id_origin'));

            if(!$felInvoiceRequest){
                return response()->json([
                    "success" => false,
                    "msg" => "Factura no encontrada para emitir"
                ]);
            }
            
            $felInvoiceRequest->setAccessToken()->sendInvoiceToFel();

            $felInvoiceRequest->invoiceDateUpdatedAt();
            
            $hashid = new Hashids(config('ninja.hash_salt'), 10);

            $id_origin_decode = $hashid->decode($felInvoiceRequest->id_origin)[0];

            $invoice = Invoice::find($id_origin_decode);
            UnlinkFile::dispatchNow(config('filesystems.default'), $invoice->client->invoice_filepath() . $invoice->number . '.pdf');

            $success = true;

            fel_register_historial($felInvoiceRequest);

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

            $felInvoiceRequest->setAccessToken()->sendUpdateInvoiceToFel();

            $felInvoiceRequest->invoiceDateUpdatedAt();

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
