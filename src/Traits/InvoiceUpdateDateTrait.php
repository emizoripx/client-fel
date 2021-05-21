<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Models\Invoice;

use Carbon\Carbon;
use Hashids\Hashids;

trait InvoiceUpdateDateTrait{

    public function invoiceDateUpdatedAt(){

        // $hashid = new Hashids(config('ninja.hash_salt'), 10);
        // $id_origin_decode = $hashid->decode($this->id_origin)[0];

        $invoice = Invoice::withTrashed()->where('id', $this->id_origin )->firstOrFail();
        
        if(!is_null($invoice)){
            
            $invoice->touch();
        }

    }

    public function invoiceDateUpdate(){
        $invoice = Invoice::withTrashed()->where('id', $this->id_origin )->firstOrFail();

        if(!is_null($invoice)){
            // Carbon::createFromFormat('Y-m-d H:i:s.u', '2019-02-01 03:45:27.612584');
            $invoice->date = date("Y-m-d", strtotime(Carbon::now()));
            $invoice->save();
        }
    }
}