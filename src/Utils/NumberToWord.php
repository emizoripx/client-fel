<?php
namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Lib\NumberToWordHelper;
use Luecano\NumeroALetras\NumeroALetras;

class NumberToWord {

    public static function getToWord($number, $decimal, $currency) 
    {
        $formatter = new NumberToWordHelper();
        return str_replace("CON","",$formatter->toInvoice($number, $decimal, $currency)) ;

    }

}