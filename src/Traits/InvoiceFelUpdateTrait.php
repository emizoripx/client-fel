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

        $felInvoiceRequest = $this->fel_invoice;

        if (empty($this->fel_invoice)) {
            bitacora_warning("EMIT INVOICE", "From Company:" . $this->company_id . ", Invoice #" . $this->numeroFactura . " does not exist yet in table FEL_INVOICE_REQUEST.");
            throw new ClientFelException(" La Factura #" . $this->numeroFactura . " no cuenta con datos necesarios para emitirse.");
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
            bitacora_info("EMIT INVOICE", "From Company:" . $this->fel_invoice->company_id . ", Invoice #" . $this->fel_invoice->numeroFactura . " was emitted succesfully.");

            return $this;

        } catch (ClientFelException $ex) {
            
            bitacora_error("UpdateFelInvoiceTrait", $ex->getMessage());

            fel_register_historial($felInvoiceRequest, $ex->getMessage());

            $felInvoiceRequest->update([
                'errores' => json_encode([[
                    'code' => 666,
                    'description' => $ex->getMessage()
                ]]),
                'estado' => $felInvoiceRequest->getInvoiceState(InvoiceStates::INVOICE_STATE_SIN_INVALID)
            ]);

            return $this;
        }
    }
}