<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Company;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelInvoiceStatusHistorial;
use EmizorIpx\ClientFel\Traits\InvoiceValidateStateTrait;
use EmizorIpx\ClientFel\Utils\InvoiceStates;
use EmizorIpx\ClientFel\Utils\Log;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use Illuminate\Http\Request;

class WebhookController extends BaseController
{
    use InvoiceValidateStateTrait;

    public function callback(Request $request)
    {
        \Log::debug('WEBHOOK-CONTROLLER INICIO CALLBACK *******************************************************');
        \Log::debug($request->all());
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

        if(isset($data['package_id'])){
            \Log::debug("WEBHOOK-CONTROLLER UPDATE PACKAGE ID");
            $felInvoice = FelInvoiceRequest::withTrashed()->where('cuf', $data['cuf'])->first();
            
            if (!empty($felInvoice)){ 
                \Log::debug("WEBHOOK-CONTROLLER UPDATE INVOICE ID #". $felInvoice->cuf );
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

            }
            else
                \Log::debug('WEBHOOK-CONTROLLER invoice package was not found');
        } else{
    
            $invoice = FelInvoiceRequest::withTrashed()->where('cuf', $data['cuf'])->first();
    
            if (empty($invoice)) {
                \Log::debug('WEBHOOK-CONTROLLER invoice was not found');
            } else {
                \Log::debug('WEBHOOK-CONTROLLER saving status and sin errors');

                $invoice->saveState($data['state'])
                    ->saveStatusCode($data['status_code'])
                    ->saveSINErrors($data['sin_errors'])
                    ->saveUrlSin($data['urlSin'])
                    ->saveEmisionType($data['emission_type'])
                    ->saveXmlUrl($data['xml_url'])
                    ->saveAddressInvoice($data['direccion'])
                    ->save();


                \Log::debug('WEBHOOK-CONTROLLER validating status invoice');

                
                $this->validateStateCode($data['status_code'], $invoice);

                \Log::debug('WEBHOOK-CONTROLLER validate invoice date update');

                $invoice->invoiceDateUpdatedAt();
                
                \Log::debug('WEBHOOK-CONTROLLER registering historial');

                fel_register_historial($invoice, $data['sin_errors'], $data['reception_code']);

                $stateInvalid = ['INVOICE_STATE_SIN_INVALID', 'INVOICE_STATE_SENT_TO_SIN_INVALID'];
                if (in_array($data['state'], $stateInvalid)) {
                    if (!$invoice->felCompany()->checkIsPostpago()) {
                        FelCompanyDocumentSector::getCompanyDocumentSectorByCode($invoice->felCompany()->id, $invoice->type_document_sector_id)->addNumberInvoice()->setCounter(-1)->save();
                    } else {
                        FelCompanyDocumentSector::getCompanyDocumentSectorByCode($invoice->felCompany()->id, $invoice->type_document_sector_id)->setPostpagoCounter(-1)->setCounter(-1)->save();
                    }
                }
            }
            \Log::debug('WEBHOOK-CONTROLLER FIN=======================================');

        }
        
    }
}
