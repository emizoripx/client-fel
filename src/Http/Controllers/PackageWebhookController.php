<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Company;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelInvoiceStatusHistorial;
use EmizorIpx\ClientFel\Traits\InvoiceValidateStateTrait;
use EmizorIpx\ClientFel\Utils\InvoiceStates;
use EmizorIpx\ClientFel\Utils\Log;
use EmizorIpx\ClientFel\Utils\PackageStates;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use Illuminate\Http\Request;

class PackageWebhookController extends BaseController {
    

    public function callback(Request $request){

        \Log::debug('WEBHOOK-PACKAGE INICIO CALLBACK *******************************************************');
        \Log::debug($request->all());

        $data = $request->all();
        $packageData = $data['package_data'];

        \Log::debug("WEBHOOK-PACKAGE UPDATE PACKAGE ID");

        if($packageData['state'] == PackageStates::PACKAGE_STATE_SENT_TO_SIN || $packageData['state'] == PackageStates::PACKAGE_STATE_SENT_TO_SIN_INVALID){

            \Log::debug("WEBHOOK-PACKAGE UPDATE STATE - SENT TO SIN");

            FelInvoiceRequest::where('package_id', $data['package_id'])->update([
                'estado' => $packageData['state'] == PackageStates::PACKAGE_STATE_SENT_TO_SIN ? PackageStates::get(PackageStates::PACKAGE_STATE_SENT_TO_SIN) : PackageStates::get(PackageStates::PACKAGE_STATE_SENT_TO_SIN_INVALID),
                'codigoEstado' => $packageData['status_code']
            ]);

        }
        if( $packageData['state'] == PackageStates::PACKAGE_STATE_SIN_VALID || $packageData['state'] == PackageStates::PACKAGE_STATE_SIN_INVALID ){

            \Log::debug("WEBHOOK-PACKAGE UPDATE STATE - STATE VALIDATION");
            
            FelInvoiceRequest::where('package_id', $data['package_id'])->update([
                'codigoEstado' => 690,
                'estado' => PackageStates::get(PackageStates::PACKAGE_STATE_SIN_VALID)
            ]);
            
            if(isset($packageData['sin_errors'])){
                \Log::debug("WEBHOOK-PACKAGE UPDATE INVOICE SIN ERRORS");
    
                $sinErrors = collect($packageData['sin_errors'])->groupBy('index_file');
    
                foreach ($sinErrors as $indexPackage => $invoiceSinErrors) {
                    \Log::debug("WEBHOOK-PACKAGE UPDATE INVOICE SIN ERRORS - INDEX #". $indexPackage);
                    FelInvoiceRequest::where('package_id', $data['package_id'])->where('index_package', $indexPackage)->update([
                        'estado' => PackageStates::get($packageData['state']),
                        'codigoEstado' => $packageData['status_code'],
                        'errores' => $invoiceSinErrors
                    ]);
                }
            }
        }


    }

}