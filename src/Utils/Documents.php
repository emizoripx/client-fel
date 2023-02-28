<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Builders\NotaEntregaBuilder;
use EmizorIpx\ClientFel\Builders\NotaRecepcionBuilder;
use EmizorIpx\ClientFel\Builders\PlanillaBuilder;

class Documents{

    const DOCUMENTO_PLANILLA = 1;

    const NOTA_ENTREGA = 2;

    const NOTA_RECEPCION = 3;

    public static function getInstanceByName($name): string
    {

        switch ($name) {
            case static::DOCUMENTO_PLANILLA:
                return PlanillaBuilder::class;
                break;
            case static::NOTA_ENTREGA:
                return NotaEntregaBuilder::class;
                break;
            case static::NOTA_RECEPCION:
                return NotaRecepcionBuilder::class;
                break;
        }
    }
}