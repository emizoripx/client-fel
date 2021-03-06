<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Utils\Ninja;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasReversionRevoked;
use EmizorIpx\ClientFel\Events\Invoice\InvoiceWasRevoked;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelInvoiceStatusHistorial;
use Exception;

trait InvoiceFelRevocateTrait{
    
    public function revocate(){
        $success = false;
        $felInvoiceRequest = $this->fel_invoice;

        try {
            \Log::debug('Model');
            \Log::debug($felInvoiceRequest);

            if(is_null($felInvoiceRequest) || is_null($felInvoiceRequest->cuf)){
                return;
            }

            if(!is_null($felInvoiceRequest->getDeletedAt())){
                throw new ClientFelException(json_encode(["errors"=>["La Factura ya fue anulada"]]));
            }
            
            $codigoMotivoAnulacion = request('codigo_motivo_anulacion');
            \Log::debug('Codigo Motivo Anulación '. request('codigo_motivo_anulacion'));

            if(!isset($codigoMotivoAnulacion)){
                throw new ClientFelException(json_encode(["errors"=>["Código Motivo de Anulación es requerido"]]));
            }

            $felInvoiceRequest->setAccessToken()->sendRevocateInvoiceToFel($codigoMotivoAnulacion);

            $felInvoiceRequest->invoiceDateUpdatedAt();

            $success = true;

            bitacora_info("FelInvoiceRequestRevocate", $success);

            event(new InvoiceWasRevoked($felInvoiceRequest->invoice_origin(), $felInvoiceRequest->invoice_origin()->company, Ninja::eventVars(auth()->user()->id)));

            fel_register_historial($felInvoiceRequest);
            
        } catch (ClientFelException $ex) {
            bitacora_error("FelInvoiceRequestRevocate", "Error al anular Factura ". $ex->getMessage());

            fel_register_historial($felInvoiceRequest, $ex->getMessage());

            throw new Exception($ex->getMessage());
        }
    }


    public function reversionRevocate(){
        $success = false;

        $felInvoiceRequest = $this->fel_invoice;

        

        try {

            if(is_null($felInvoiceRequest->getDeletedAt())){
                throw new ClientFelException(json_encode(["errors" => ["La factura no fue anulada"]]));
            }
            
            if(is_null($felInvoiceRequest->cuf)){
                $felInvoiceRequest->restoreInvoice();
                return;
            }


            $felInvoiceRequest->setAccessToken()->sendReversionRevocateInvoiceToFel();

            $felInvoiceRequest->invoiceDateUpdatedAt();

            $success = true;


            event(new InvoiceWasReversionRevoked($felInvoiceRequest->invoice_origin(), $felInvoiceRequest->invoice_origin()->company, Ninja::eventVars(auth()->user()->id)));

            bitacora_info("FelInvoiceRequest:ReversionRevocate", $success);


            fel_register_historial($felInvoiceRequest);

        } catch (ClientFelException $ex) {
            bitacora_error("FelInvoiceRequest:ReversionRevocate", "Error al desanular Factura ".$ex->getMessage());

            fel_register_historial($felInvoiceRequest, $ex->getMessage());

            throw new Exception($ex->getMessage());
        }
    }
}