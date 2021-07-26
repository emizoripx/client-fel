<?php

namespace EmizorIpx\ClientFel\Utils;

class PackageStates {

    const PACKAGE_STATE_QUEUE_PENDING = "PACKAGE_STATE_QUEUE_PENDING";
    const PACKAGE_STATE_IN_QUEUE = "PACKAGE_STATE_IN_QUEUE";
    const PACKAGE_STATE_SENT_TO_SIN = "PACKAGE_STATE_SENT_TO_SIN";
    const PACKAGE_STATE_SENT_TO_SIN_INVALID = "PACKAGE_STATE_SENT_TO_SIN_INVALID";
    const PACKAGE_STATE_SIN_VALID = "PACKAGE_STATE_SIN_VALID";
    const PACKAGE_STATE_SIN_INVALID = "PACKAGE_STATE_SIN_INVALID";
    const PACKAGE_STATE_INTERNAL_ERROR = "PACKAGE_STATE_INTERNAL_ERROR";

    public static function get($value){

        switch ($value) {
            case static::PACKAGE_STATE_QUEUE_PENDING:
                return "PENDIENTE";
                break;
            case static::PACKAGE_STATE_IN_QUEUE:
                return "EN ESPERA";
                break;
            case static::PACKAGE_STATE_SENT_TO_SIN:
                return "ENVIADA";
                break;
            case static::PACKAGE_STATE_SENT_TO_SIN_INVALID:
                return "ERROR DE RECEPCION";
                break;
            case static::PACKAGE_STATE_SIN_VALID:
                return "VALIDO";
                break;
            case static::PACKAGE_STATE_SIN_INVALID:
                return "INVALIDO";
                break;
            case static::PACKAGE_STATE_INTERNAL_ERROR:
                return "ERROR INTERNO";
                break;
            
            default:
                return "ESTADO NO REGISTRADO";
                break;
        }

    }

}