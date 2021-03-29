<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Company;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelInvoiceStatusHistorial;
use EmizorIpx\ClientFel\Traits\InvoiceValidateStateTrait;
use EmizorIpx\ClientFel\Utils\InvoiceStates;
use EmizorIpx\ClientFel\Utils\Log;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use Illuminate\Http\Request;

class WebhookController extends BaseController
{
    use InvoiceValidateStateTrait;

    public function callback(Request $request)
    {
        \Log::debug('Recibo');
        \Log::debug($request->all());
        
        $data = $request->all();
        
        $invoice = FelInvoiceRequest::withTrashed()->where('cuf', $data['cuf'])->first();

        \Log::debug('Webhook Model');
        \Log::debug($invoice);

        $invoice->saveState($data['state'])->saveStatusCode($data['status_code'])->saveSINErrors($data['sin_errors'])->save();

        $this->validateStateCode($data['status_code'], $invoice);

        fel_register_historial($invoice, $data['sin_errors'], $data['reception_code']);

        if(!$invoice->prepagoAccount()->checkIsPostpago()){
            $stateInvalid = ['INVOICE_STATE_SIN_INVALID', 'INVOICE_STATE_SENT_TO_SIN_INVALID'];
            if(in_array($data['state'], $stateInvalid)){
                $invoice->prepagoAccount()->addNumberInvoice()->save();
            }

        }


        
    }
}
