<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Http\Requests\CobrosQRInvoiceDeleteRequest;
use EmizorIpx\ClientFel\Http\Requests\CobrosQRInvoiceStoreRequest;
use EmizorIpx\ClientFel\Services\Cobrosqr\CobrosqrService;

class CobrosqrController extends BaseController
{
    public function store(CobrosQRInvoiceStoreRequest $request)
    {
        
        $service = new CobrosqrService();
        $service->createInvoice($request->all());

        return response()->json(["success" => 200]);

    }

    public function delete(CobrosQRInvoiceDeleteRequest $request)
    {
        $service = new CobrosqrService();
        $service->unlinkImei($request->all());

        return response()->json(["success" => 200]);
    }
}
