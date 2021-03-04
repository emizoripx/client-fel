<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Utils\TypeDocuments;
use Hashids\Hashids;

trait InvoiceFelEmitTrait{

    public function emit(){

        $success = false;
        $felInvoiceRequest = $this->fel_invoice;


        
        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        
        $company_id = $hashid->decode($felInvoiceRequest->company_id);

        $access_token = FelClientToken::getTokenByAccount($company_id);

        try {


            $felInvoiceRequest->sendInvoiceToFel($access_token);


            $success = true;
            
            bitacora_info("Shop emit", $success);

        } catch (ClientFelException $ex) {
            bitacora_error("ShopEmit", "Error emit invoice ". $ex->getMessage());
            
        }
    }
} 