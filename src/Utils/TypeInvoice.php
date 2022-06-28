<?php

namespace EmizorIpx\ClientFel\Utils;

class TypeInvoice{

    const DERECHO_CREDITO_FISCAL = [1,2,11,12,13,14,15,16,17,18,19,21,22,23,25,26,35,37];
    const SIN_DERECHO_CREDITO_FISCAL = [3,4,5,6,7,8,9,10,20,27,28,5];
    


    public static function getTypeInvoice($code){ 
        switch ($code) {
            case (in_array($code, static::DERECHO_CREDITO_FISCAL)):
                return 'Con Derecho a Crédito Fiscal';
                break;
            case (in_array($code, static::SIN_DERECHO_CREDITO_FISCAL)):
                return 'Sin Derecho a Crédito Fiscal';
                break;
            
        }
    }

}