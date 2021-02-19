<?php
namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

trait InvoiceParametersTrait {

    public function fel_invoice()
    {
        return $this->hasOne(FelInvoiceRequest::class,'id_origin');
    }

    public function getCufAttribute()
    {
        
        if ( empty($this->fel_invoice) ) 
            return "";
        
        return $this->fel_invoice->cuf;
    }
}