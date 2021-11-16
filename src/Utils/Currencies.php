<?php

namespace EmizorIpx\ClientFel\Utils;

class Currencies{
    const DOLAR = 2;
    const BOLIVIANO = 1;

    public static function getShortCode($code){

        switch ($code) {
            case self::DOLAR:
                return 'USD';
                break;
            case self::BOLIVIANO:
                return 'Bs';
                break;
            
        }
        
    }
    public static function getDescriptionCurrency($code){

        switch ($code) {
            case self::DOLAR:
                return 'Dólares Americanos';
                break;
            case self::BOLIVIANO:
                return 'Bolivianos';
                break;
            
        }
        
    }
    public static function getSingularDescriptionCurrency($code){

        switch ($code) {
            case self::DOLAR:
                return 'Dólar';
                break;
            case self::BOLIVIANO:
                return 'Boliviano';
                break;
            
        }
        
    }

}