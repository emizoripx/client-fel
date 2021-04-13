<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Builders\CompraVentaBuilder;

class TypeDocumentSector
{

    const COMPRA_VENTA = 1;
    const EXPORTACION_MINERALES = 20;

    public static function getInstanceByCode($code)
    {
        
        switch ($code) {
            case static::COMPRA_VENTA:
                return CompraVentaBuilder::class;
                break;
            case static::EXPORTACION_MINERALES:
                return ExportacionMineralesBuilder::class;
                break;
            
            default:
                return CompraVentaBuilder::class;
                break;
        }
        
    }
}
