<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Utils\Log;
use Illuminate\Http\Request;

class WebhookController extends BaseController
{

    public function callback(Request $request)
    {
        \Log::debug('Recibo');
        \Log::debug($request->all());

        return response()->json([
            'msg' => 'Recibido',
            'data' => $request->all()
        ]);
        
    }
}
