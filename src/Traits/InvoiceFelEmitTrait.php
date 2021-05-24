<?php

namespace EmizorIpx\ClientFel\Traits;

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
        
        $felInvoiceRequest = $this->fel_invoice;

        if (empty($this->fel_invoice)) {
            bitacora_warning("EMIT INVOICE", "From Company:" . $this->company_id . ", Invoice #" . $this->numeroFactura . " does not exist yet in table FEL_INVOICE_REQUEST.");
            throw new ClientFelException(" La Factura #" . $this->numeroFactura . " no cuenta con datos necesarios para emitirse.");
            return $this;
        }

        try {

            
            if ($felInvoiceRequest->codigoEstado != null || $felInvoiceRequest->cuf != null){
                return $this;
            }

            $felInvoiceRequest->setAccessToken()->sendInvoiceToFel();

            $felInvoiceRequest->deletePdf();

            bitacora_info("EMIT INVOICE", "From Company:" . $this->fel_invoice->company_id . ", Invoice #" . $this->fel_invoice->numeroFactura . " was emitted succesfully.");

            return $this;
        } catch (ClientFelException $ex) {

            bitacora_error("EMIT INVOICE", "From Company:" . $this->fel_invoice->company_id . ", Invoice #" . $this->fel_invoice->numeroFactura . " was NOT emitted." . "Error emit invoice " . $ex->getMessage());
            // throw new Exception( $ex->getMessage() );

            $felInvoiceRequest->update([
                'errores' => json_encode([
                    'evento' => 'Emitir',
                    'error' => $ex->getMessage()
                ]),
                'estado' => $felInvoiceRequest->getInvoiceState(InvoiceStates::INVOICE_STATE_SIN_INVALID)
            ]);

            return $this;
        }
    }
}
