<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Builders\CompraVentaBuilder;
use EmizorIpx\ClientFel\Builders\ExportacionMineralesBuilder;

class TypeDocumentSector
{

    const COMPRA_VENTA = 1;
    const EXPORTACION_MINERALES = 20;

    public static function getInstanceByCode($code):string
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

    public static function getFelDocumentNameByCode($code):string
    {
        switch ($code) {
            case static::COMPRA_VENTA:
                return 'compra-venta';
                break;
            case static::EXPORTACION_MINERALES:
                return 'comercial-exportacion-minerales';
                break;

            default:
                return 'compra-venta';
                break;
        }
    }
    
}
