<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Company;
use App\Models\Invoice;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelInvoiceStatusHistorial;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Traits\InvoiceValidateStateTrait;
use EmizorIpx\ClientFel\Utils\InvoiceStates;
use EmizorIpx\ClientFel\Utils\Log;
use EmizorIpx\ClientFel\Utils\PackageStates;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PackageWebhookController extends BaseController {
    

    public function callback(Request $request){

        \Log::debug('WEBHOOK-PACKAGE INICIO CALLBACK *******************************************************');
        \Log::debug($request->all());

        $data = $request->all();
        $packageData = $data['package_data'];

        \Log::debug("WEBHOOK-PACKAGE UPDATE PACKAGE ID");

        if($packageData['state'] == PackageStates::PACKAGE_STATE_SENT_TO_SIN || $packageData['state'] == PackageStates::PACKAGE_STATE_SENT_TO_SIN_INVALID){

            \Log::debug("WEBHOOK-PACKAGE UPDATE STATE - SENT TO SIN");

            $affect = FelInvoiceRequest::where('package_id', $data['package_id'])->where('uuid_package', $data['uuid_package'])->update([
                'estado' => $packageData['state'] == PackageStates::PACKAGE_STATE_SENT_TO_SIN ? PackageStates::get(PackageStates::PACKAGE_STATE_SENT_TO_SIN) : PackageStates::get(PackageStates::PACKAGE_STATE_SENT_TO_SIN_INVALID),
                'codigoEstado' => $packageData['status_code']
            ]);

            if($packageData['state'] == PackageStates::PACKAGE_STATE_SENT_TO_SIN_INVALID){
                $invoice = FelInvoiceRequest::where('package_id', $data['package_id'])->where('uuid_package', $data['uuid_package'])->first();
                // TODO: updated invoice available
                $this->updateNumberInvoiceAvailable($invoice, $affect);
            }

        }
        if( $packageData['state'] == PackageStates::PACKAGE_STATE_SIN_VALID || $packageData['state'] == PackageStates::PACKAGE_STATE_SIN_INVALID ){

            \Log::debug("WEBHOOK-PACKAGE UPDATE STATE - STATE VALIDATION");
            
            FelInvoiceRequest::where('package_id', $data['package_id'])->where('uuid_package', $data['uuid_package'])->update([
                'codigoEstado' => 690,
                'estado' => PackageStates::get(PackageStates::PACKAGE_STATE_SIN_VALID)
            ]);
            
            if(isset($packageData['sin_errors'])){
                \Log::debug("WEBHOOK-PACKAGE UPDATE INVOICE SIN ERRORS");
    
                $sinErrors = collect($packageData['sin_errors'])->groupBy('index_file');
    
                foreach ($sinErrors as $indexPackage => $invoiceSinErrors) {
                    \Log::debug("WEBHOOK-PACKAGE UPDATE INVOICE SIN ERRORS - INDEX #". $indexPackage);
                    $invoice = FelInvoiceRequest::where('package_id', $data['package_id'])->where('uuid_package', $data['uuid_package'])->where('index_package', $indexPackage)->first();
                    $invoice->update([
                        'estado' => PackageStates::get($packageData['state']),
                        'codigoEstado' => $packageData['status_code'],
                        'errores' => $invoiceSinErrors
                    ]);

                    $this->updateNumberInvoiceAvailable($invoice, 1);

                }
            }
        }

        $list_invoices = FelInvoiceRequest::where('package_id', $data['package_id'])->where('uuid_package', $data['uuid_package'])->pluck('id_origin');
        Invoice::whereIn('id',$list_invoices)->update(['updated_at' => Carbon::now()]);

    }

    public function updateNumberInvoiceAvailable($invoice, $affect = 1){
        if(!$invoice->felCompany()->checkIsPostpago()){
            FelCompanyDocumentSector::getCompanyDocumentSectorByCode($invoice->felCompany()->id, $invoice->type_document_sector_id)->addNumberInvoice($affect)->setCounter(-1 * $affect)->save();
        } else {
            FelCompanyDocumentSector::getCompanyDocumentSectorByCode($invoice->felCompany()->id, $invoice->type_document_sector_id)->setPostpagoCounter( -1 * $affect)->setCounter(-1 * $affect)->save();
        }
    }

}