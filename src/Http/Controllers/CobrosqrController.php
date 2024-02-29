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
        cobrosqr_logging("STORE>start #".$request->get('ticket') . "  request => " . json_encode($request->all()));
        $service = new CobrosqrService();
        $service->createInvoice($request->all());
        cobrosqr_logging("STORE>end  #" . $request->get('ticket'));
        return response()->json(["success" => 200]);

    }

    public function delete(CobrosQRInvoiceDeleteRequest $request)
    {
        cobrosqr_logging("UNLINK>start #" . $request->get('imei'));
        $service = new CobrosqrService();
        $service->unlinkImei($request->all());
        cobrosqr_logging("UNLINK>end  #" . $request->get('imei'));
        return response()->json(["success" => 200]);
    }
}
