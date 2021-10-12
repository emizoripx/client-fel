<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Models\FelInvoice;
use EmizorIpx\ClientFel\Services\Invoices\Invoices;
use Hashids\Hashids;
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

            $parse_response = $invoice_service->sendToFel();

            \Log::debug(">>>>>>>>>>>>>>>>>>>>>>>>> SAVING CUF 2 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<");
            \DB::table('fel_invoice_requests')->whereId($this->id)->update(['cuf' => $parse_response['cuf']]);
            
            $input = $invoice_service->getInvoiceByCuf();

            
            $hashid = new Hashids (config('ninja.hash_salt'),10);
            
            $input['id_origin'] = $hashid->decode($this->hashed_id)[0] . "";

            Log::debug("response invoice with origin => " . json_encode($input));

            FelInvoice::create($input);

            return true;

        } catch(ClientFelException $ex) {
            Log::debug("problems  " . json_encode($ex->getMessage()) );
            throw $ex;

        }

    }
}
