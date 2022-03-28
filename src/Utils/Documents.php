<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Builders\PlanillaBuilder;

class Documents{

    const DOCUMENTO_PLANILLA = 1;

    public static function getInstanceByName($name): string
    {

        switch ($name) {
            case static::DOCUMENTO_PLANILLA:
                return PlanillaBuilder::class;
                break;
        }
    }
}