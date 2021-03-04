<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Jobs\Util\UnlinkFile;
use App\Models\Invoice;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Utils\TypeDocuments;
use Hashids\Hashids;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{

    public function emit(Request $request)
    {

        
        $success = false;

        $access_token = FelClientToken::getTokenByAccount($request->company_id);

        try {

            $felInvoiceRequest = FelInvoiceRequest::findByIdOrigin($request->input('id_origin'));

            
            $felInvoiceRequest->sendInvoiceToFel($access_token);
            
            $hashid = new Hashids(config('ninja.hash_salt'), 10);

            $id_origin_decode = $hashid->decode($felInvoiceRequest->id_origin)[0];

            $invoice = Invoice::find($id_origin_decode);
            UnlinkFile::dispatchNow(config('filesystems.default'), $invoice->client->invoice_filepath() . $invoice->number . '.pdf');

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
