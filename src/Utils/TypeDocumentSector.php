<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Builders\ComercialExportacionBuilder;
use EmizorIpx\ClientFel\Builders\CompraVentaBuilder;
use EmizorIpx\ClientFel\Builders\CreditoDebitoBuilder;
use EmizorIpx\ClientFel\Builders\ExportacionMineralesBuilder;
use EmizorIpx\ClientFel\Builders\VentaMineralesBuilder;

class TypeDocumentSector
{

    const COMPRA_VENTA = 1;
    const ALQUILER_BIENES_INMUEBLES = 2;
    const COMERCIAL_EXPORTACION = 3;
    const COMERCIAL_EXPORTACION_LIBRE_CONSIGNACION = 4;
    const ZONA_FRANCA = 5;
    const SERVICIO_TURISTICO_HOSPEDAJE = 6;
    const COMERCIALIZACION_ALIMENTOS_SEGURIDAD  = 7;
    const TASA_CERO = 8;
    const COMPRA_VENTA_MONEDA_EXTRANJERA  = 9;
    const DUTTY_FREE  = 10;
    const SECTORES_EDUCATIVOS = 11;
    const COMERCIALIZACION_HIDROCARBUROS = 12;
    const SERVICIOS_BASICOS = 13;
    const PRODUCTOS_ALCANZADOS_ICE = 14;
    const ENTIDADES_FINANCIERAS = 15;
    const HOTELES = 16;
    const HOSPITALES_CLINICAS = 17;
    const JUEGOS_AZAR = 18;
    const HIDROCARBUROS = 19;
    const EXPORTACION_MINERALES = 20;
    const VENTA_INTERNA_MINERALES = 21;
    const TELECOMUNICACIONES = 22;
    const PREVALORADA = 23;
    const DEBITO_CREDITO = 24;
    const PRODUCTOS_NACIONALES = 25;
    const PRODUCTOS_NACIONALES_ICE = 26;
    const REGIMEN_7RG = 27;
    const COMERCIAL_EXPORTACION_SERVICIOS = 28;

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
            case static::COMERCIAL_EXPORTACION:
                return ComercialExportacionBuilder::class;
                break;
            case static::DEBITO_CREDITO:
                return CreditoDebitoBuilder::class;
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
            case static::COMERCIAL_EXPORTACION:
                return 'comercial-exportacion';
                break;
            case static::DEBITO_CREDITO:
                return 'nota-debito-credito';
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
            case static::ALQUILER_BIENES_INMUEBLES:
                return 'RECIBO DE ALQUILER DE BIENES INMUEBLES';
                break;
            
            case static::COMERCIAL_EXPORTACION:
                return 'FACTURA COMERCIAL DE EXPORTACIÓN';
                break;
            case static::COMERCIAL_EXPORTACION_LIBRE_CONSIGNACION:
                return 'FACTURA COMERCIAL DE EXPORTACIÓN EN LIBRE CONSIGNACIÓN';
                break;
            case static::ZONA_FRANCA:
                return 'FACTURA DE ZONA FRANCA';
                break;
            case static::SERVICIO_TURISTICO_HOSPEDAJE:
                return 'FACTURA DE SERVICIO TURÍSTICO Y HOSPEDAJE';
                break;
            case static::COMERCIALIZACION_ALIMENTOS_SEGURIDAD:
                return 'FACTURA DE COMERCIALIZACIÓN DE ALIMENTOS – SEGURIDAD';
                break;
            
            case static::TASA_CERO:
                return 'FACTURA DE TASA CERO POR VENTA DE LIBROS Y TRANSPORTE INTERNACIONAL DE CARGA';
                break;
            case static::COMPRA_VENTA_MONEDA_EXTRANJERA:
                return 'FACTURA DE COMPRA Y VENTA DE MONEDA EXTRANJERA';
                break;
            case static::DUTTY_FREE:
                return 'FACTURA DUTTY FREE';
                break;
            
            case static::SECTORES_EDUCATIVOS:
                return 'FACTURA SECTORES EDUCATIVOS';
                break;
            case static::COMERCIALIZACION_HIDROCARBUROS:
                return 'FACTURA DE COMERCIALIZACIÓN DE HIDROCARBUROS';
                break;
            
            case static::SERVICIOS_BASICOS:
                return 'FACTURA DE SERVICIOS BÁSICOS';
                break;
            case static::PRODUCTOS_ALCANZADOS_ICE:
                return 'FACTURA PRODUCTOS ALCANZADOS POR EL ICE';
                break;
            
            case static::ENTIDADES_FINANCIERAS:
                return 'FACTURA DE ENTIDADES FINANCIERAS';
                break;
            
            case static::HOTELES:
                return 'FACTURA DE HOTELES';
                break;
            case static::HOSPITALES_CLINICAS:
                return 'FACTURA DE HOSPITALES/CLÍNICAS';
                break;
            case static::JUEGOS_AZAR:
                return 'FACTURA DE JUEGOS DE AZAR';
                break;
            case static::HIDROCARBUROS:
                return 'FACTURA HIDROCARBUROS';
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
            case static::PREVALORADA:
                return 'FACTURA PREVALORADA';
                break;

            case static::DEBITO_CREDITO:
                return 'NOTA DÉBITO CRÉDITO';
                break;
            case static::PRODUCTOS_NACIONALES:
                return 'FACTURA DE PRODUCTOS NACIONALES';
                break;
            case static::PRODUCTOS_NACIONALES_ICE:
                return 'FACTURA DE PRODUCTOS NACIONALES - ICE';
                break;
            case static::REGIMEN_7RG:
                return 'FACTURA REGIMEN 7RG';
                break;
            case static::COMERCIAL_EXPORTACION_SERVICIOS:
                return 'FACTURA COMERCIAL DE EXPORTACIÓN DE SERVICIOS';
                break;
            
            
        }

    }
    
}
