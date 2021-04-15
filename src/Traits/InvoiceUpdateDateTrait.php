<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Models\Invoice;

use Carbon\Carbon;
use Hashids\Hashids;

trait InvoiceUpdateDateTrait{

    public function invoiceDateUpdatedAt(){

        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        $id_origin_decode = $hashid->decode($this->id_origin)[0];

        $invoice = Invoice::withTrashed()->where('id', $id_origin_decode )->firstOrFail();

        \Log::debug('Updating updated_at of Invoice.......');
        
        if(!is_null($invoice)){
            
            $invoice->touch();
        }

    }
}