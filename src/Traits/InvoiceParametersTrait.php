<?php
namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Http\Resources\InvoiceResource;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

trait InvoiceParametersTrait {

    public function fel_invoice()
    {
        return $this->hasOne(FelInvoiceRequest::class,'id_origin', 'id')->withTrashed();
    }

    public function includeFelData(){
        $invoice = $this->fel_invoice;

        return is_null($invoice) ? null : new InvoiceResource($invoice);
    }

    public function getCufAttribute()
    {
        
        if ( empty($this->fel_invoice) ) 
            return "";
        
        return $this->fel_invoice->cuf;
    }

    public function getCodigoEstadoAttribute()
    {
        
        if ( empty($this->fel_invoice) ) 
            return null;
        
        return $this->fel_invoice->codigoEstado;
    }

    public function getEstadoAttribute()
    {
        
        if ( empty($this->fel_invoice) ) 
            return "";
        
        return $this->fel_invoice->estado;
    }
    public function getErroresAttribute()
    {
        
        if ( empty($this->fel_invoice) ) 
            return "";
        
        return $this->fel_invoice->errores;
    }
}