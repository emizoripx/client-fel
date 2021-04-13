<?php

namespace EmizorIpx\ClientFel\Traits;

use Luecano\NumeroALetras\NumeroALetras;

trait NumberToWordTrait{
    public function getToWord($number, $decimal, $currency){
        $formatter = new NumeroALetras();
        return $formatter->toInvoice($number, $decimal, $currency);
    }
}