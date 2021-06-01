<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Utils\InvoiceStates;
use Exception;

trait InvoiceFelUpdateTrait{

    public function updateEmitedInvoice($should_emit = 'true'){
        $success = false;

        if ($should_emit !== 'true')
            return $this;

        $felInvoiceRequest = $this->invoice->fel_invoice->fresh();


        if (empty($this->invoice->fel_invoice)) {
            bitacora_warning("EMIT INVOICE", "From Company:" . $this->invoice->company_id . ", Invoice #" . $this->invoice->numeroFactura . " does not exist yet in table FEL_INVOICE_REQUEST.");
            throw new ClientFelException(" La Factura #" . $this->invoice->numeroFactura . " no cuenta con datos necesarios para emitirse.");
            return $this;
        }

        try {
            if($felInvoiceRequest->codigoEstado == 690){
                \Log::debug("Factura ya emitida");
                throw new ClientFelException('La factura ya fue emitida');
            }
            
            $felInvoiceRequest->setAccessToken()->sendUpdateInvoiceToFel();

            $felInvoiceRequest->deletePdf();


            fel_register_historial($felInvoiceRequest);
            bitacora_info("EMIT INVOICE", "From Company:" . $this->invoice->fel_invoice->company_id . ", Invoice #" . $this->invoice->fel_invoice->numeroFactura . " was emitted succesfully.");

            return $this;

        } catch (ClientFelException $ex) {
            
            bitacora_error("UpdateFelInvoiceTrait", $ex->getMessage());

            fel_register_historial($felInvoiceRequest, $ex->getMessage());

            throw new ClientFelException( $ex->getMessage() );

            return $this;
        }
    }
}