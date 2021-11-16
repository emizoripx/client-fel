<?php
namespace EmizorIpx\ClientFel\Utils;

use Luecano\NumeroALetras\NumeroALetras;

class NumberToWord {

    public static function getToWord($number, $decimal, $currency) 
    {
        $formatter = new NumeroALetras();
        return str_replace("CON","",$formatter->toInvoice($number, $decimal, $currency)) ;

    }

}