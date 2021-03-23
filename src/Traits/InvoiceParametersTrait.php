<?php
namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Models\FelInvoiceRequest;

trait InvoiceParametersTrait {

    public function fel_invoice()
    {
        return $this->hasOne(FelInvoiceRequest::class,'id_origin')->withTrashed();
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
            return "";
        
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