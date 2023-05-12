<?php
namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Http\Resources\InvoiceResource;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

trait InvoiceParametersTrait {

    public function fel_invoice()
    {
        return $this->hasOne(FelInvoiceRequest::class,'id_origin', 'id')->withTrashed();
    }

    public function getEmailAgency()
    {
        return $this->fel_invoice()->first()->getEmailAgency();
    }

    public function includeFelData(){
        $invoice = $this->fel_invoice;

        return is_null($invoice) ? null : new InvoiceResource($invoice);
    }

  
}