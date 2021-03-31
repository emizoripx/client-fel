<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use Exception;

trait InvoiceFelUpdateTrait{

    public function updateEmitedInvoice(){
        $success = false;

        $felInvoiceRequest = $this->fel_invoice;

        try {
            
            $felInvoiceRequest->setAccessToken()->sendUpdateInvoiceToFel();

            $success = true;

            bitacora_info("UpdateFelInvoiceTrait", $success);

            fel_register_historial($felInvoiceRequest);

        } catch (ClientFelException $ex) {
            
            bitacora_error("UpdateFelInvoiceTrait", $ex->getMessage());

            fel_register_historial($felInvoiceRequest, $ex->getMessage());

            throw new Exception($ex->getMessage());
        }
    }
}