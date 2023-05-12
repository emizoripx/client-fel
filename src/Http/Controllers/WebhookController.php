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
                $invoiceUpdate = FelInvoiceRequest::whereId($invoice->id)->select('id', 'id_origin','cuf', 'codigoEstado', 'estado', 'urlSin', 'emission_type', 'xml_url', 'direccion', 'ack_ticket')->first();
                \Log::debug('WEBHOOK-CONTROLLER =====> ACTUALIZANDO factura....');
                $invoiceUpdate->cuf = $data['cuf'];
                $invoiceUpdate->estado = in_array($data['status_code'],[908,690]) ? "VALIDA" : ( $data['status_code'] == 902 ? "INVALIDA": ( in_array($data['status_code'], [691, 905]) ? "ANULADA":"" ));
                $invoiceUpdate->codigoEstado = $data['status_code'];
                $invoiceUpdate->urlSin = isset($data['urlSin']) ? $data['urlSin'] . "&t=2" : "";
                $invoiceUpdate->emission_type =  $data['emission_type'] == 1  ?"En línea" : "Fuera de línea";
                $invoiceUpdate->xml_url = $data['xml_url'];
                $invoiceUpdate->direccion = $data['direccion'];
                $invoiceUpdate->package_id = $data['package_id'];
                $invoiceUpdate->uuid_package = $data['uuid_package'];
                $invoiceUpdate->index_package = $data['index_package'];
                $invoiceUpdate->save();
                $invoiceUpdate->touchPdf();
            }
            \Log::debug('WEBHOOK-CONTROLLER FIN CALLBACK INVOICE *******************************************************');
    }
}
