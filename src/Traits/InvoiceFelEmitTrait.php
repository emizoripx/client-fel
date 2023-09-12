<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Utils\Ninja;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasEmited;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Utils\InvoiceStates;
use Exception;

trait InvoiceFelEmitTrait
{

    public function emit( $should_emit = 'true')
    {

        
        //if should invoice is not set, then not emit
        if ($should_emit !== 'true')
            return $this;
        
        $info = request("tstms_small") . "EMIT-TRAIT  ";
        info($info .  "INVOICE_ID=  [" . $this->invoice->id . "] ");
        
        $felInvoiceRequest = $this->invoice->fel_invoice->fresh();


        if (empty($this->invoice->fel_invoice)) {
            bitacora_warning("EMIT INVOICE", "From Company:" . $this->invoice->company_id . ", Invoice #" . $this->invoice->number . " does not exist yet in table FEL_INVOICE_REQUEST.");
            throw new ClientFelException(" La Factura #" . $this->invoice->number . " no cuenta con datos necesarios para emitirse.");
            return $this;
        }
        info($info .  " FEL_INVOICE = " . $felInvoiceRequest->id );
        try {
            info($info .  "set invoice number ". $this->invoice->number);
            // save number in felinvoicerequest 
            $felInvoiceRequest->setNumeroFactura($this->invoice->number);
            // reload changes in model
            $felInvoiceRequest = $felInvoiceRequest->fresh();

            $felInvoiceRequest->setAccessToken()->sendInvoiceToFel();

            $invoice = $felInvoiceRequest->invoice_origin();

            $invoice->service()->markSent()->save();

            $felInvoiceRequest->setEmittedByUser();

            $felInvoiceRequest->savePolicyCnc();
            
            event(new InvoiceWasEmited($felInvoiceRequest->invoice_origin(), $felInvoiceRequest->invoice_origin()->company, Ninja::eventVars(auth()->user() ? auth()->user()->id : null)));

            bitacora_info("EMIT INVOICE", "From Company:" . $this->invoice->fel_invoice->company_id . ", Invoice #" . $this->invoice->fel_invoice->numeroFactura . " was emitted succesfully.");
            info($info .  " OK INVOICE_NUMBER=" . $felInvoiceRequest->numeroFactura);
            return $this;
        } catch (ClientFelException $ex) {
            logger()->error($info .  "ERROR" . $ex->getMessage());
            bitacora_error("EMIT INVOICE", "From Company:" . $this->invoice->fel_invoice->company_id . ", Invoice #" . $this->invoice->fel_invoice->numeroFactura . " was NOT emitted." . "Error emit invoice " . $ex->getMessage());
            throw new ClientFelException( $ex->getMessage() );

            return $this;
        }
    }


    public function xml_file_path()
    {
        try {

            $felInvoiceRequest = $this->fel_invoice->fresh();   

        } catch( \Throwable $ex ) {
            \Log::debug("No se pudo obtener el XML: " . $ex->getMessage());
            return null;
        }
        
        if (empty($felInvoiceRequest)) {
            return null;
        }

        if (is_null($felInvoiceRequest->cuf)) {
            return null;            
        }
        \Log::debug("REQUEST FOR ATTACHMENT IN SEND INVOICE  XML>>>>>> " . $felInvoiceRequest->xml_url);
        return $felInvoiceRequest->xml_url;

    }
}
