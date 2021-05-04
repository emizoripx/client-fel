<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Builders\CompraVentaBuilder;
use EmizorIpx\ClientFel\Builders\ExportacionMineralesBuilder;
use EmizorIpx\ClientFel\Builders\VentaMineralesBuilder;

class TypeDocumentSector
{

    const COMPRA_VENTA = 1;
    const COMERCIAL_EXPORTACION = 3;
    const TASA_CERO = 8;
    const SECTORES_EDUCATIVOS = 11;
    const SERVICIOS_BASICOS = 13;
    const ENTIDADES_FINANCIERAS = 15;
    const HOTELES = 16;
    const EXPORTACION_MINERALES = 20;
    const VENTA_INTERNA_MINERALES = 21;
    const TELECOMUNICACIONES = 21;

    public static function getInstanceByCode($code):string
    {
        
        switch ($code) {
            case static::COMPRA_VENTA:
                return CompraVentaBuilder::class;
                break;
            case static::EXPORTACION_MINERALES:
                return ExportacionMineralesBuilder::class;
                break;
            case static::VENTA_INTERNA_MINERALES:
                return VentaMineralesBuilder::class;
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
            case static::VENTA_INTERNA_MINERALES:
                return 'venta-interna-minerales';
                break;

            default:
                return 'compra-venta';
                break;
        }
    }

    public static function getName($code){

        switch ($code) {
            case static::COMPRA_VENTA:
                return 'FACTURA COMPRA-VENTA';
                break;
            
            case static::COMERCIAL_EXPORTACION:
                return 'FACTURA COMERCIAL DE EXPORTACIÓN';
                break;
            
            case static::TASA_CERO:
                return 'FACTURA DE TASA CERO POR VENTA DE LIBROS Y TRANSPORTE INTERNACIONAL DE CARGA';
                break;
            
            case static::SECTORES_EDUCATIVOS:
                return 'FACTURA SECTORES EDUCATIVOS';
                break;
            
            case static::SERVICIOS_BASICOS:
                return 'FACTURA DE SERVICIOS BÁSICOS';
                break;
            
            case static::ENTIDADES_FINANCIERAS:
                return 'FACTURA DE ENTIDADES FINANCIERAS';
                break;
            
            case static::HOTELES:
                return 'FACTURA DE HOTELES';
                break;
            
            case static::EXPORTACION_MINERALES:
                return 'FACTURA COMERCIAL DE EXPORTACIÓN DE MINERALES';
                break;
            
            case static::VENTA_INTERNA_MINERALES:
                return 'FACTURA VENTA INTERNA MINERALES';
                break;
            
            case static::TELECOMUNICACIONES:
                return 'FACTURA TELECOMUNICACIONES';
                break;
            
            
        }

    }
    
}
