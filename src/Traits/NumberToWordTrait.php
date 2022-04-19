<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Lib\NumberToWordHelper;
use Luecano\NumeroALetras\NumeroALetras;

trait NumberToWordTrait{
    public function getToWord($number, $decimal, $currency){
        $formatter = new NumberToWordHelper();
        return str_replace("CON","",$formatter->toInvoice($number, $decimal, $currency)) ;
    }
}