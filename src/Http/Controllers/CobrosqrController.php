<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Http\Requests\CobrosQRInvoiceDeleteRequest;
use EmizorIpx\ClientFel\Http\Requests\CobrosQRInvoiceStoreRequest;
use EmizorIpx\ClientFel\Models\CompanyUserTerminal;
use App\Models\PaymentHash;
use EmizorIpx\ClientFel\Services\Cobrosqr\CobrosqrService;
use EmizorIpx\ClientFel\Services\Cobrosqr\CobrosqrTerminalService;
use Illuminate\Http\Request;
use App\Models\CompanyGateway;
class CobrosqrController extends BaseController
{
    use MakesHash;
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

    // public function scan(Request $request)
    // {
        
    //     try {
    //         $service = new CobrosqrTerminalService();
    //         $service->scan($request->get("code"));
    //         return response()->json(["success" => true]);
    //     } catch (\Throwable $th) {
    //         return response()->json(["success" => false, "data" => $th->getMessage()]);
    //     }

    // }


    public function listPayments(Request $request)
    {
        try {
            $service = new CobrosqrTerminalService();
            $data = $service->listPayments();
            
            return response()->json(["success" => true, "message" => $data]);
        } catch (\Throwable $th) {
            info("error " . $th->getLine() . "    file " . $th->getFile() );
            return response()->json(["success" => false, "message" => $th->getMessage()]);
        }
    }
    public function listCashClosures(Request $request)
    {
        try {
            $service = new CobrosqrTerminalService();
            $data = $service->listCashClosures();
            
            return response()->json(["success" => true, "message" => $data]);
        } catch (\Throwable $th) {
            info("error " . $th->getLine() . "    file " . $th->getFile() );
            return response()->json(["success" => false, "message" => $th->getMessage()]);
        }
    }

    public function checkQrId(Request $request)
    {
        try{

            $payment_hash = PaymentHash::whereRaw('BINARY `hash`= ?', [$request->get("qr_id")])->first();
            $qr_paid = !is_null($payment_hash->payment_id);

            if ($qr_paid){
                return response()->json(["success" => true, "message" => $payment_hash->payment->transaction_reference , "payment_id" => $this->encodePrimaryKey($payment_hash->payment_id) ] );
            }
            return response()->json(["success" => true, "message" => "Esperando pago...", "payment_id" => null ] );

        } catch (\Throwable $th) {
            info("error " . $th->getLine() . "    file " . $th->getFile() );
            return response()->json(["success" => false, "message" => $th->getMessage()]);
        }
    }

    public function callbackPayment(Request $request)
    {

        $payment_hash = PaymentHash::whereRaw('BINARY `hash`= ?', [$request->input("qr_id")])->first();

        if (empty($payment_hash)){
            info("---------------");
            return response()->json(["success" => true, "message" => "Ok"]);
        }

        $invoice = $payment_hash->fee_invoice;

        try {
            $company_gateway = CompanyGateway::whereCompanyId($invoice->company_id)->whereGatewayKey('d14dd26a47cec830x11x5700bfb67ccc')->first();
            $cobrosqrservice = new CobrosqrTerminalService();
            $cobrosqrservice->setCompanyGateway($company_gateway);
            $cobrosqrservice->callbackPayment($payment_hash, $request->only(["payment_date","payment_amount","qr_id","payment_name","payment_bank"]));

            return response()->json(["success" => true, "message" => "Ok"]);
        } catch (\Throwable $th) {
            info("error " . $th->getMessage() . "  file:" . $th->getFile() . " line " . $th->getLine() );
            return response()->json(["success" => true, "message" => "Ok"]);
        }
    }
}
