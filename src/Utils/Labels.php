<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Models\BitacoraLog;

class Labels
{
    public static function getUnitPrice($nit){
        if($nit == '1020415021'){
            return ctrans('texts.unit_cost_msc');
        }

        return ctrans('texts.unit_cost');
    }
    public static function getCodeProduct($nit){
        if($nit == '1020415021'){
            return ctrans('texts.item_msc');
        }

        return ctrans('texts.item');
    }
    
}
