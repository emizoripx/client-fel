<?php

namespace EmizorIpx\ClientFel\Utils;

class TypeInvoice{

    const DERECHO_CREDITO_FISCAL = 'FACTURA CON DERECHO A CREDITO FISCAL';
    const SIN_DERECHO_CREDITO_FISCAL = 'FACTURA SIN DERECHO A CREDITO FISCAL';
    


    public static function getTypeInvoice($type){
        switch ($type) {
            case static::DERECHO_CREDITO_FISCAL:
                return 'Con Derecho a Crédito Fiscal';
                break;
            case static::SIN_DERECHO_CREDITO_FISCAL:
                return 'Sin Derecho a Crédito Fiscal';
                break;
            
        }
    }

}