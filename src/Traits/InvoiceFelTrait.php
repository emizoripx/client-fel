<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelInvoice;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use EmizorIpx\ClientFel\Utils\TypeDocuments;
use Illuminate\Support\Facades\Log;

trait InvoiceFelTrait
{

    public function createInvoiceFel()
    {

        $access_token = FelClientToken::getTokenByAccount($this->company_id);

        try{
                        
            $invoice_service = new Invoices;
            
            $invoice_service->setAccessToken($access_token);
            
            $invoice_service->setBranchNumber(0);
            
            $invoice_service->buildData($this);

            $invoice_service->setTypeDocument(TypeDocuments::COMPRA_VENTA);

            $invoice_service->sendToFel();

            $invoice_service->setCuf($invoice_service->getResponse()['cuf']);

            $input = $invoice_service->getInvoiceByCuf();

            Log::debug("response invoice => " . json_encode($input));
            
            FelInvoice::create($input);

            return true;

        } catch(ClientFelException $ex) {
            Log::debug("problems  " . json_encode($ex->getMessage()) );
            throw $ex;

        }

    }
}
