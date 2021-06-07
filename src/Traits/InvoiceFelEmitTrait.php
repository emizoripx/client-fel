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
        
        $felInvoiceRequest = $this->invoice->fel_invoice->fresh();


        if (empty($this->invoice->fel_invoice)) {
            bitacora_warning("EMIT INVOICE", "From Company:" . $this->invoice->company_id . ", Invoice #" . $this->invoice->numeroFactura . " does not exist yet in table FEL_INVOICE_REQUEST.");
            throw new ClientFelException(" La Factura #" . $this->invoice->numeroFactura . " no cuenta con datos necesarios para emitirse.");
            return $this;
        }

        try {

            
            if ($felInvoiceRequest->codigoEstado != null || $felInvoiceRequest->cuf != null){
                return $this;
            }

            $felInvoiceRequest->setAccessToken()->sendInvoiceToFel();

            $felInvoiceRequest->deletePdf();

            event(new InvoiceWasEmited($felInvoiceRequest->invoice_origin(), $felInvoiceRequest->invoice_origin()->company, Ninja::eventVars(auth()->user()->id)));

            bitacora_info("EMIT INVOICE", "From Company:" . $this->invoice->fel_invoice->company_id . ", Invoice #" . $this->invoice->fel_invoice->numeroFactura . " was emitted succesfully.");

            return $this;
        } catch (ClientFelException $ex) {

            bitacora_error("EMIT INVOICE", "From Company:" . $this->invoice->fel_invoice->company_id . ", Invoice #" . $this->invoice->fel_invoice->numeroFactura . " was NOT emitted." . "Error emit invoice " . $ex->getMessage());
            throw new ClientFelException( $ex->getMessage() );

            return $this;
        }
    }
}
