<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\TypeDocuments;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{

    public function emit(Request $request)
    {

        
        $success = false;

        $access_token = FelClientToken::getTokenByAccount($request->company_id);

        try {

            $felInvoiceRequest = FelInvoiceRequest::findByIdOrigin($request->input('id_origin'));

            
            
            $invoice_service = new Invoices;

            $invoice_service->setAccessToken($access_token);

            $invoice_service->setBranchNumber(0);

            $invoice_service->buildData($felInvoiceRequest);

            $invoice_service->setTypeDocument(TypeDocuments::COMPRA_VENTA);

            $invoice_service->sendToFel();

            $felInvoiceRequest->saveCuf($invoice_service->getResponse()['cuf']);
            // $input = $invoice_service->getInvoiceByCuf();

            // FelInvoice::create($input);

            $success = true;

            return response()->json([
                "success" => $success
            ]);

        } catch (ClientFelException $ex) {
            return response()->json([
                "success" => false,
                "msg" => $ex->getMessage()
            ]);
        }
    }
}
