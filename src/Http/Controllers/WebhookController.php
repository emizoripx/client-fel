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

        $data = $request->all();

        if(isset($data['package_id'])){
            \Log::debug("WEBHOOK-CONTROLLER UPDATE PACKAGE ID");
            $felInvoice = FelInvoiceRequest::where('ack_ticket', $data['ack_ticket'])->first();

            if (!empty($felInvoice)) 
                $felInvoice->savePackageId($data['package_id'])
                ->saveState($data['state'])
                ->saveStatusCode($data['status_code'])
                ->saveIndexPackage($data['index_package'])
                ->save();
            else
                \Log::debug(' WEBHOOK-CONTROLLER invoice package was not found');
        } else{

            if (isset($data['ack_ticket'])  ){
    
                \Log::debug(' WEBHOOK-CONTROLLER ack_ticket used');
                $invoice = FelInvoiceRequest::withTrashed()->where('ack_ticket', $data['ack_ticket'])->first();
            } else{
                \Log::debug(' WEBHOOK-CONTROLLER cuf used');
                $invoice = FelInvoiceRequest::withTrashed()->where('cuf', $data['cuf'])->first();
            }
    
            if (empty($invoice)) {
                \Log::debug(' WEBHOOK-CONTROLLER invoice was not found');
            }
    
            \Log::debug(' WEBHOOK-CONTROLLER saving status and sin errors');

            $invoice->saveState($data['state'])->saveStatusCode($data['status_code'])->saveSINErrors($data['sin_errors'])->save();

            \Log::debug(' WEBHOOK-CONTROLLER validating status invoice');

            $this->validateStateCode($data['status_code'], $invoice);

            \Log::debug(' WEBHOOK-CONTROLLER validate invoice date update');

            $invoice->invoiceDateUpdatedAt();

            \Log::debug(' WEBHOOK-CONTROLLER registering historial');
            
            fel_register_historial($invoice, $data['sin_errors'], $data['reception_code']);
            
            $stateInvalid = ['INVOICE_STATE_SIN_INVALID', 'INVOICE_STATE_SENT_TO_SIN_INVALID'];
            if(in_array($data['state'], $stateInvalid)){
                if(!$invoice->felCompany()->checkIsPostpago()){
                    FelCompanyDocumentSector::getCompanyDocumentSectorByCode($invoice->felCompany()->id, $invoice->type_document_sector_id)->addNumberInvoice()->setCounter(-1)->save();
                } else {
                    FelCompanyDocumentSector::getCompanyDocumentSectorByCode($invoice->felCompany()->id, $invoice->type_document_sector_id)->setPostpagoCounter(-1)->setCounter(-1)->save();
                }
    
            }
            \Log::debug(' WEBHOOK-CONTROLLER FIN=======================================');

        }
        
    }
}
