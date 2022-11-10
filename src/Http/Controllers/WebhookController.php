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
            'facturaTicket' => $request->input('fiscalDocumentCode', null),
        ];

        // if (isset($data['package_id'])) {
        //     \Log::debug('WEBHOOK-CONTROLLER INICIO CALLBACK PACKAGES *******************************************************');

        //     \Log::debug($request->all());
        //     \Log::debug("WEBHOOK-CONTROLLER UPDATE PACKAGE ID");
        //     $felInvoice = FelInvoiceRequest::withTrashed()->where('cuf', $data['cuf'])->first();

        //     if (!empty($felInvoice)) {
        //         \Log::debug("WEBHOOK-CONTROLLER UPDATE INVOICE ID #" . $felInvoice->cuf);
        //         $felInvoice->savePackageId($data['package_id'])
        //             ->saveState($data['state'])
        //             ->saveStatusCode($data['status_code'])
        //             ->saveIndexPackage($data['index_package'])
        //             ->saveUrlSin($data['urlSin'])
        //             ->saveEmisionType($data['emission_type'])
        //             ->saveXmlUrl($data['xml_url'])
        //             ->saveAddressInvoice($data['direccion'])
        //             ->saveUuidPackage($data['uuid_package'])
        //             ->save();
        //         \Log::debug('WEBHOOK-CONTROLLER FIN CALLBACK PACKAGES *******************************************************');
        //     } else {
        //         \Log::debug('WEBHOOK-CONTROLLER invoice package was not found');
        //     }
        // } else{
            \Log::debug('WEBHOOK-CONTROLLER INICIO CALLBACK INVOICE *******************************************************');

            if ( !is_null($data['facturaTicket']) ) {
                \Log::debug('WEBHOOK-CONTROLLER ==> probando con factura ticket ' . $data['facturaTicket']);
                $invoice = \DB::table('fel_invoice_requests')->select('id')->where('factura_ticket',$data['facturaTicket'])->first();
            }

            if( empty($invoice) && !is_null($data['ack_ticket'])){
                \Log::debug('WEBHOOK-CONTROLLER ==> probando con ACK TICKET ' . $data['ack_ticket']);
                $invoice = \DB::table('fel_invoice_requests')->select('id')->where('ack_ticket', $data['ack_ticket'])->first();
            }

            if ( empty($invoice) && !is_null($data['cuf']) ){
                \Log::debug('WEBHOOK-CONTROLLER ==> probando con CUF ' . $data['cuf']);
                $invoice = \DB::table('fel_invoice_requests')->select('id')->where('cuf', $data['cuf'])->first();
            }

            if (!empty($invoice)) {
                $invoiceUpdate = FelInvoiceRequest::whereId($invoice->id)->select('id', 'cuf', 'codigoEstado', 'estado', 'urlSin', 'emission_type', 'xml_url', 'direccion', 'ack_ticket')->first();
                \Log::debug('WEBHOOK-CONTROLLER =====> ACTUALIZANDO factura....');
                $invoiceUpdate->cuf = $data['cuf'];
                $invoiceUpdate->estado = in_array($data['status_code'],[908,690]) ? "VALIDA" : ( $data['status_code'] == 902 ? "INVALIDA": ( in_array($data['status_code'], [691, 905]) ? "ANULADA":"" ));
                $invoiceUpdate->codigoEstado = $data['status_code'];
                $invoiceUpdate->urlSin = $data['urlSin'];
                $invoiceUpdate->emission_type =  $data['emission_type'] == 1  ?"En línea" : "Fuera de línea";
                $invoiceUpdate->xml_url = $data['xml_url'];
                $invoiceUpdate->direccion = $data['direccion'];
                $invoiceUpdate->save();
            }
            \Log::debug('WEBHOOK-CONTROLLER FIN CALLBACK INVOICE *******************************************************');

        }

    // }
}
