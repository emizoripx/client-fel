<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Utils\Log;
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

        
    }
}
