<?php

namespace EmizorIpx\ClientFel\Utils;

class Currencies{
    const DOLAR = 2;
    const BOLIVIANO = 1;

    public static function getShortCode($code){

        switch ($code) {
            case self::DOLAR:
                return '$us';
                break;
            case self::BOLIVIANO:
                return 'Bs';
                break;
            
        }
        
    }

}