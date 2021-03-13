<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Company;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Utils\Log;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use Illuminate\Http\Request;

class WebhookController extends BaseController
{

    public function callback(Request $request)
    {
        \Log::debug('Recibo');
        \Log::debug($request->all());
        
        $data = $request->all();
        
        $invoice = FelInvoiceRequest::where('cuf', $data['cuf'])->first();

        $invoice->saveState($data['state'])->saveStatusCode($data['status_code'])->saveSINErrors($data['sin_errors'])->save();

        if(!$invoice->prepagoAccount()->checkIsPostpago()){
            $stateInvalid = ['INVOICE_STATE_SIN_INVALID', 'INVOICE_STATE_SENT_TO_SIN_INVALID'];
            if(in_array($data['state'], $stateInvalid)){
                $invoice->prepagoAccount()->addNumberInvoice()->save();
            }

        }


        
    }
}
