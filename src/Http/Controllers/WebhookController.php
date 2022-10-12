<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Traits\InvoiceValidateStateTrait;
use Illuminate\Http\Request;

class WebhookController extends BaseController
{
    use InvoiceValidateStateTrait;

    public function callback(Request $request)
    {
        sleep(2);
        $data = [
            'package_id' => $request->input('package_id', null),
            'ack_ticket' => $request->input('ack_ticket', null),
            'state' => $request->input('state', null),
            'status_code' => $request->input('status_code', null),
            'index_package' => $request->input('index_package', null),
            'uuid_package' => $request->input('uuid_package', null),
            'cuf' => $request->input('cuf', null),
            'urlSin' => $request->input('urlSin', null),
            'emission_type' => $request->input('emission_type_code', null),
            'direccion' => $request->input('direccion', null),
            'sin_errors' => $request->input('sin_errors', null),
            'reception_code' => $request->input('reception_code', null),
            'xml_url' => $request->input('xml_url', null),
        ];

        if (isset($data['package_id'])) {
            \Log::debug('WEBHOOK-CONTROLLER INICIO CALLBACK *******************************************************');

            \Log::debug($request->all());
            \Log::debug("WEBHOOK-CONTROLLER UPDATE PACKAGE ID");
            $felInvoice = FelInvoiceRequest::withTrashed()->where('cuf', $data['cuf'])->first();

            if (!empty($felInvoice)) {
                \Log::debug("WEBHOOK-CONTROLLER UPDATE INVOICE ID #" . $felInvoice->cuf);
                $felInvoice->savePackageId($data['package_id'])
                    ->saveState($data['state'])
                    ->saveStatusCode($data['status_code'])
                    ->saveIndexPackage($data['index_package'])
                    ->saveUrlSin($data['urlSin'])
                    ->saveEmisionType($data['emission_type'])
                    ->saveXmlUrl($data['xml_url'])
                    ->saveAddressInvoice($data['direccion'])
                    ->saveUuidPackage($data['uuid_package'])
                    ->save();
            } else
                \Log::debug('WEBHOOK-CONTROLLER invoice package was not found');
        }

        else{
            $invoice = FelInvoiceRequest::withTrashed()->where('ack_ticket', $data['ack_ticket'])->select('id','xm_url','direccion')->first();
            if (!empty($invoice)) {
            
                \Log::debug('WEBHOOK-CONTROLLER saving XML_URL');

                $invoice->saveXmlUrl($data['xml_url'])
                    ->saveAddressInvoice($data['direccion'])
                    ->save();
             
            }
            

        }

    }
}
