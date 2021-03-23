<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use Exception;

trait InvoiceFelRevocateTrait{
    
    public function revocate(){
        $success = false;
        $felInvoiceRequest = $this->fel_invoice;

        try {
            \Log::debug('Model');
            \Log::debug($felInvoiceRequest);

            if(is_null($felInvoiceRequest->cuf)){
                return;
            }

            if(!is_null($felInvoiceRequest->getRevocationReasonCode())){
                throw new ClientFelException(json_encode(["errors"=>["La Factura ya fue anulada"]]));
            }
            
            $codigoMotivoAnulacion = request('codigo_motivo_anulacion');
            \Log::debug('Codigo Motivo Anulación '. request('codigo_motivo_anulacion'));

            if(!isset($codigoMotivoAnulacion)){
                throw new ClientFelException(json_encode(["errors"=>["Código Motivo de Anulación es requerido"]]));
            }

            $felInvoiceRequest->setAccessToken()->sendRevocateInvoiceToFel($codigoMotivoAnulacion);

            $success = true;

            bitacora_info("FelInvoiceRequestRevocate", $success);
            
        } catch (ClientFelException $ex) {
            bitacora_error("FelInvoiceRequestRevocate", "Error al anular Factura ". $ex->getMessage());

            throw new Exception($ex->getMessage());
        }
    }
}