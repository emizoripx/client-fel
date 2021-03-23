<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Utils\InvoiceStates;

trait GetInvoiceStateTrait{

    public function getInvoiceState($value){
        switch ($value) {
            case InvoiceStates::INVOICE_STATE_IN_QUEUE:
                return "EN ESPERA";
                break;
            case InvoiceStates::INVOICE_STATE_SENT_TO_SIN:
                return "ENVIADO";
                break;
            case InvoiceStates::INVOICE_STATE_SENT_TO_SIN_INVALID:
                return "ERROR DE RECEPCION";
                break;
            case InvoiceStates::INVOICE_STATE_SIN_VALID:
                return "VALIDO";
                break;
            case InvoiceStates::INVOICE_STATE_SIN_INVALID:
                return "INVALIDO";
                break;
            case InvoiceStates::INVOICE_STATE_INTERNAL_ERROR:
                return "ERROR INTERNO";
                break;
            case InvoiceStates::INVOICE_REVOCATION_STATE_IN_QUEUE:
                return "ANULACION EN ESPERA";
                break;
            case InvoiceStates::INVOICE_REVOCATION_STATE_SENT_TO_SIN:
                return "ANULACION ENVIADA";
                break;
            case InvoiceStates::INVOICE_REVOCATION_STATE_SENT_TO_SIN_INVALID:
                return "ERROR EN LA RECEPCION DE ANULACION";
                break;
            case InvoiceStates::INVOICE_REVOCATION_STATE_SIN_INVALID:
                return "ANULACION INVALIDA";
                break;
            case InvoiceStates::INVOICE_REVOCATION_STATE_SIN_VALID:
                return "ANULADO";
                break;
            case InvoiceStates::INVOICE_REVOCATION_STATE_INTERNAL_ERROR:
                return "ERROR INTERNO";
                break;
            
            default:
                throw new ClientFelException("ESTADO DESCONOCIDO");
                break;
        }
    }
}