<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Builders\AlcanzadaIceBuilder;
use EmizorIpx\ClientFel\Builders\AlquileresBuilder;
use EmizorIpx\ClientFel\Builders\ClinicasBuilder;
use EmizorIpx\ClientFel\Builders\ComercialConsignacionBuilder;
use EmizorIpx\ClientFel\Builders\ComercialExportacionBuilder;
use EmizorIpx\ClientFel\Builders\ComercializacionGnvBuilder;
use EmizorIpx\ClientFel\Builders\ComercializacionHidrocarburosBuilder;
use EmizorIpx\ClientFel\Builders\CompraVentaBonificacionesBuilder;
use EmizorIpx\ClientFel\Builders\CompraVentaBuilder;
use EmizorIpx\ClientFel\Builders\CreditoDebitoBuilder;
use EmizorIpx\ClientFel\Builders\EngarrafadorasBuilder;
use EmizorIpx\ClientFel\Builders\EntidadFinancieraBuilder;
use EmizorIpx\ClientFel\Builders\ExportacionMineralesBuilder;
use EmizorIpx\ClientFel\Builders\ExportacionServiciosBuilder;
use EmizorIpx\ClientFel\Builders\HidrocarburosIehdBuilder;
use EmizorIpx\ClientFel\Builders\HidrocarburosNoIehdBuilder;
use EmizorIpx\ClientFel\Builders\HotelesBuilder;
use EmizorIpx\ClientFel\Builders\NotaConciliacionBuilder;
use EmizorIpx\ClientFel\Builders\PrevaloradaBuilder;
use EmizorIpx\ClientFel\Builders\PrevaloradaSdcfBuilder;
use EmizorIpx\ClientFel\Builders\SectorEducativoBuilder;
use EmizorIpx\ClientFel\Builders\SectorEducativoZonaFrancaBuilder;
use EmizorIpx\ClientFel\Builders\SegurosBuilder;
use EmizorIpx\ClientFel\Builders\ServiciosBasicosBuilder;
use EmizorIpx\ClientFel\Builders\TasaCeroBuilder;
use EmizorIpx\ClientFel\Builders\TelecomunicacionesBuilder;
use EmizorIpx\ClientFel\Builders\TurismoBuilder;
use EmizorIpx\ClientFel\Builders\VentaMineralesBuilder;
use EmizorIpx\ClientFel\Builders\ZonaFrancaBuilder;

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
    const HIDROCARBUROS_IEHD = 19;
    const EXPORTACION_MINERALES = 20;
    const VENTA_INTERNA_MINERALES = 21;
    const TELECOMUNICACIONES = 22;
    const PREVALORADA = 23;
    const DEBITO_CREDITO = 24;
    const PRODUCTOS_NACIONALES = 25;
    const PRODUCTOS_NACIONALES_ICE = 26;
    const REGIMEN_7RG = 27;
    const COMERCIAL_EXPORTACION_SERVICIOS = 28;
    const NOTA_CONCILIACION = 29;
    const SEGUROS = 34;
    const COMPRA_VENTA_BONIFICACIONES = 35;
    const PREVALORADA_SDCF = 36;
    const COMERCIALIZACION_GNV = 37;
    const HIDROCARBUROS_NO_IEHD = 38;
    const SECTOR_EDUCATIVO_ZONA_FRANCA = 46;
    const ENGARRAFADORAS = 51;

    const ARRAY_NAMES = [
        1 => "Factura compra venta",
        2 => "Recibo de Alquiler de Bienes Inmuebles",
        3 => "Factura comercial de exportación",
        4 => "Factura Comercial de Exportación en Libre Consignación",
        5 => "Factura de Zona Franca",
        6 => "Factura de Servicio Turístico y Hospedaje",
        7 => "Factura de Comercialización de Alimentos – Seguridad ",
        8 => "Factura de tasa cero por venta de libros y transporte internacional de carga",
        9 => "Factura de Compra y Venta de Moneda Extranjera ",
        10 => "Factura Dutty Free",
        11 => "Factura sectores educativos",
        12 => "Factura de Comercialización de Hidrocarburos",
        13 => "Servicios básicos",
        14 => "Factura Productos Alcanzados por el ICE",
        15 => "Factura Entidades Financieras",
        16 => "Factura de hoteles",
        17 => "Factura de Hospitales/Clínicas",
        18 => "Factura de Juegos de Azar",
        19 => "Factura De Hidrocarburos Alcanzada IEHD",
        20 => "Factura de exportación de minerales",
        21 => "Factura de venta interna de minerales",
        22 => "Factura telecomunicaciones",
        23 => "Factura Prevalorada",
        24 => "Nota débito crédito",
        25 => "Factura de Productos Nacionales",
        26 => "Factura de Productos Nacionales - ICE",
        27 => "Factura Regimen 7RG",
        28 => "Factura Comercial de Exportación de Servicios",
        29 => "Nota de Conciliación",
        30 => "Boleto Aereo",
        31 => "Factura De Suministro",
        32 => "Factura ICE Zona Franca",
        33 => "Factura Tasa Cero Bienes Capital",
        34 => "Factura Seguros",
        35 => "Factura compra venta bonificaciones",
        36 => "Factura Prevalorada SDCF",
        37 => "Factura De Comercialización De GNV",
        38 => "Factura De Hidrocarburos No Alcanzada IEHD",
        39 => "Factura Comercializacion GN y GLP",
        40 => "Factura De Servicios Básicos ZF",
        41 => "Factura Compra Venta Tasas",
        42 => "Factura Alquiler ZF",
        43 => "Factura Comercial De Exportación Hidrocarburos",
        44 => "Factura Importacion Comercializacion Lubricantes",
        45 => "Factura Comercial De Exportacion Precio Venta",
        46 => "Factura Sectores Educativo Zona Franca",
        51 => "Factura Engarrafadoras",
    ];

    public static function getInstanceByCode($code):string
    {
        
        switch ($code) {
            case static::COMPRA_VENTA:
                return CompraVentaBuilder::class;
                break;
            case static::PREVALORADA:
                return PrevaloradaBuilder::class;
                break;
            case static::ALQUILER_BIENES_INMUEBLES:
                return AlquileresBuilder::class;
                break;
            case static::COMERCIAL_EXPORTACION_LIBRE_CONSIGNACION:
                return ComercialConsignacionBuilder::class;
                break;
            case static::ZONA_FRANCA:
                return ZonaFrancaBuilder::class;
                break;
            case static::TASA_CERO:
                return TasaCeroBuilder::class;
                break;
            case static::EXPORTACION_MINERALES:
                return ExportacionMineralesBuilder::class;
                break;
            case static::SECTORES_EDUCATIVOS:
                return SectorEducativoBuilder::class;
                break;
            case static::COMERCIALIZACION_HIDROCARBUROS:
                return ComercializacionHidrocarburosBuilder::class;
                break;
            case static::COMERCIALIZACION_GNV:
                return ComercializacionGnvBuilder::class;
                break;
            case static::SERVICIOS_BASICOS:
                return ServiciosBasicosBuilder::class;
                break;
            case static::HIDROCARBUROS_IEHD:
                return HidrocarburosIehdBuilder::class;
                break;
            case static::HOTELES:
                return HotelesBuilder::class;
                break;
            case static::HOSPITALES_CLINICAS:
                return ClinicasBuilder::class;
                break;
            case static::VENTA_INTERNA_MINERALES:
                return VentaMineralesBuilder::class;
                break;
            case static::COMERCIAL_EXPORTACION:
                return ComercialExportacionBuilder::class;
                break;
            case static::TELECOMUNICACIONES:
                return TelecomunicacionesBuilder::class;
                break;
            case static::PREVALORADA:
                return PrevaloradaBuilder::class;
                break;
            case static::DEBITO_CREDITO:
                return CreditoDebitoBuilder::class;
                break;
            case static::COMERCIAL_EXPORTACION_SERVICIOS:
                return ExportacionServiciosBuilder::class;
                break;
            case static::NOTA_CONCILIACION:
                return NotaConciliacionBuilder::class;
                break;
            case static::SEGUROS:
                return  SegurosBuilder::class;
                break;
            case static::COMPRA_VENTA_BONIFICACIONES:
                return CompraVentaBonificacionesBuilder::class;
                break;
            case static::HIDROCARBUROS_NO_IEHD :
                return HidrocarburosNoIehdBuilder::class ;
                break;
            case static::SECTOR_EDUCATIVO_ZONA_FRANCA :
                return SectorEducativoZonaFrancaBuilder::class;
                break;
            case static::ENGARRAFADORAS :
                return EngarrafadorasBuilder::class;
                break;
            case static::PRODUCTOS_ALCANZADOS_ICE :
                return AlcanzadaIceBuilder ::class;
                break;
            case static::SERVICIO_TURISTICO_HOSPEDAJE :
                return TurismoBuilder::class;
                break;
            case static::PREVALORADA_SDCF :
                return PrevaloradaSdcfBuilder::class; 
                break;
            case static::ENTIDADES_FINANCIERAS :
                return EntidadFinancieraBuilder::class; 
                break;
            
            
            default:
                return CompraVentaBuilder::class;
                break;
        }
        
    }

    public static function getFelDocumentNameByCode($code, $company_nit = null):string
    {
        switch ($code) {
            case static::COMPRA_VENTA:
                return 'compra-venta';
                break;
            case static::PREVALORADA:
                return 'prevalorada';
                break;
            case static::COMERCIAL_EXPORTACION_LIBRE_CONSIGNACION:
                return 'comercial-libre-consignacion';
                break;
            case static::ZONA_FRANCA:
                return 'venta-zona-franca';
                break;
            case static::EXPORTACION_MINERALES:
                return 'comercial-exportacion-minerales';
                break;
            case static::TASA_CERO:
                return 'tasa-cero';
                break;
            case static::SECTORES_EDUCATIVOS:
                return 'sector-educativo';
                break;
            case static::COMERCIALIZACION_HIDROCARBUROS:
                return 'comercializacion-hidrocarburos';
                break;
            case static::COMERCIALIZACION_GNV:
                return 'comercializacion-gnv';
                break;
            case static::SERVICIOS_BASICOS:
                return 'servicios-basicos';
                break;
            case static::HIDROCARBUROS_IEHD:
                return 'hidrocarburo-iehd';
                break;
            case static::HOTELES:
                return 'hoteles';
                break;
            case static::HOSPITALES_CLINICAS:
                return 'clinicas';
                break;
            case static::VENTA_INTERNA_MINERALES:
                return 'venta-interna-minerales';
                break;
            case static::COMERCIAL_EXPORTACION:
                return 'comercial-exportacion';
                break;
            case static::TELECOMUNICACIONES:
                return 'telecomunicaciones';
                break;
            case static::DEBITO_CREDITO:
                return 'nota-debito-credito';
                break;
            case static::ALQUILER_BIENES_INMUEBLES:
                return 'alquileres';
                break;
            case static::COMERCIAL_EXPORTACION_SERVICIOS:
                return 'exportacion-servicios';
                break;
            case static::NOTA_CONCILIACION:
                return 'nota-conciliacion';
                break;
            case static::SEGUROS:
                return 'seguros';
                break;
            case static::COMPRA_VENTA_BONIFICACIONES:
                return 'compra-venta-bonificacion';
                break;
            case static::HIDROCARBUROS_NO_IEHD:
                return 'hidrocarburo-noiehd';
                break;

            case static::SECTOR_EDUCATIVO_ZONA_FRANCA:
                return 'educativo-zona-franca';
                break;
            case static::ENGARRAFADORAS:
                return 'engarrafadora';
                break;
            case static::PRODUCTOS_ALCANZADOS_ICE:
                return 'alcanzada-ice';
                break;
            case static::SERVICIO_TURISTICO_HOSPEDAJE:
                return 'turismo';
                break;
            case static::PREVALORADA_SDCF:
                return 'prevalorada-sdcf';
                break;
            case static::ENTIDADES_FINANCIERAS:
                return 'entidades-financieras';
                break;


            default:
                return 'compra-venta';
                break;
        }
    }
    public static function geTemplateByCode($code, $company_nit = null):string
    {
        \Log::debug("sector document ". $code);
        switch ($code) {
            case static::COMPRA_VENTA:
                if ($company_nit == '373172026') {
                    return 'compra-venta-hurbens';
                }
                if ($company_nit == '1001665021') {
                    return 'compra-venta-carraza';
                }
                if( $company_nit == '1020415021'){
                    return 'compra-venta-msc';
                }
                if( $company_nit == '191310020'){
                    return 'compra-venta-crediseguros';
                }
                return 'compra-venta';
                break;
            case static::COMERCIAL_EXPORTACION_LIBRE_CONSIGNACION:
                return 'comercial-libre-consignacion';
                break;
            case static::ZONA_FRANCA:
                return 'zona-franca';
                break;
            case static::EXPORTACION_MINERALES:
                if( $company_nit == '421759027'){
                    return 'comercial-exportacion-minerales-sanfrancisco';
                }
                if( $company_nit == '344946021'){
                    return 'comercial-exportacion-minerales-ingenio-sansilvestre';
                }
                if( $company_nit == '301400028'){
                    return 'comercial-exportacion-minerales-roque';
                }
                if( $company_nit == '1020415021'){
                    return 'comercial-exportacion-minerales-msc';
                }
                if( $company_nit == '1017233026'){
                    return 'comercial-exportacion-minerales-manquiri';
                }
                return 'comercial-exportacion-minerales';
                break;
            case static::TASA_CERO:
                return 'tasa-cero';
                break;
            case static::SECTORES_EDUCATIVOS:
                return 'sector-educativo';
                break;
            case static::SERVICIOS_BASICOS:
                return 'servicios-basicos';
                break;
            case static::HIDROCARBUROS_IEHD:
                return 'hidrocarburos';
                break;
            case static::HOTELES:
                return 'hoteles';
                break;
            case static::VENTA_INTERNA_MINERALES:
                if( $company_nit == '1020415021'){
                    return 'venta-interna-minerales-msc';
                }
                return 'venta-interna-minerales';
                break;
            case static::COMERCIAL_EXPORTACION:
                if( $company_nit == '1004057020'){
                    return 'comercial-exportacion-andean';
                }
                if( $company_nit == '1015601025'){
                    return 'comercial-exportacion-blacutt';
                }
                if( $company_nit == '1020415021'){
                    return 'comercial-exportacion-msc';
                }
                if ($company_nit == '1015607026') {
                    return 'comercial-exportacion-urqupina';
                }
                if ($company_nit == '1005479029') {
                    return 'comercial-exportacion-saite';
                }
                return 'comercial-exportacion';
                break;
            case static::TELECOMUNICACIONES:
                return 'telecomunicaciones'; //TODO: CHANGE in case needs different template
                break;
            case static::DEBITO_CREDITO:
                return 'nota-debito-credito';
                break;
            case static::COMERCIALIZACION_HIDROCARBUROS:
                return 'comercializacion-hidrocarburos';
                break;
            case static::ALQUILER_BIENES_INMUEBLES:
                if ($company_nit == '191310020') {
                    return 'alquileres-crediseguros-personales';
                }
                return 'alquileres';
                break;
            case static::COMERCIAL_EXPORTACION_SERVICIOS:
                return 'exportacion-servicios';
                break;
            case static::NOTA_CONCILIACION:
                return 'nota-conciliacion';
                break;
            case static::SEGUROS:
                return 'seguros';
                break;
            case static::COMPRA_VENTA_BONIFICACIONES:
                return 'compra-venta-bonificaciones';
                break;
            case static::ENGARRAFADORAS:
                return 'engarrafadora';
                break;

            default:
                return 'compra-venta';
                break;
        }
    }

    public static function getTemplateByDocumentSector( $document_sector, $company_id, $branch_code = null, $thermal_printer_format = false, $typeDocument = null, $pos_code = null ){
   
        $template = \DB::table('fel_templates')
                        ->where('company_id', $company_id)
                        ->where('document_sector_code', $document_sector)
                        ->where('branch_code', $branch_code)
                        ->where( function( $query ) use ($pos_code) {

                            if( is_null($pos_code) || $pos_code == 0 ) {

                                return $query->whereNull('pos_code');
                            }

                            return $query->where('pos_code', $pos_code);

                        })
                        ->first();

        if( empty($template) ){

            if ( $typeDocument && $typeDocument == Documents::NOTA_ENTREGA ) {
                return ["templates/general/" . $document_sector . "/default_delivered_note.blade.php",false];
            }
            if ( $typeDocument && $typeDocument == Documents::NOTA_RECEPCION ) {
                return ["templates/general/" . $document_sector . "/default_received_note.blade.php", false];
            }

            if ($thermal_printer_format) 
                return ["templates/general/" . $document_sector . "/default_rollo.blade.php", false];

            return ["templates/general/". $document_sector . "/default.blade.php", false];
        }

         
        if ($typeDocument && $typeDocument == Documents::NOTA_ENTREGA ) {
            //TODO: check if exists
            $split = explode(".blade.php", $template->blade_resource);
            return [$split[0] . "_delivered_note.blade.php", false];
        } 

        if ($typeDocument && $typeDocument == Documents::NOTA_RECEPCION ) {
            //TODO: check if exists
            $split = explode(".blade.php", $template->blade_resource);
            return [$split[0] . "_received_note.blade.php", false];
        } 

        if ($thermal_printer_format) {
            //TODO: check if exists
            $split = explode(".blade.php", $template->blade_resource);
            return [$split[0] . "_rollo.blade.php", false];
        }

        return [$template->blade_resource, $template->footer_custom == 1 ? true : false];

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
                return 'FACTURA ZONA FRANCA';
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
            case static::HIDROCARBUROS_IEHD:
                return 'FACTURA DE HIDROCARBUROS ALCANZADA IEHD';
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
            case static::NOTA_CONCILIACION:
                return 'NOTA DE CONCILIACIÓN';
                break;
            case static::SEGUROS:
                return 'FACTURA SEGUROS';
                break;
            case static::COMPRA_VENTA_BONIFICACIONES:
                return 'FACTURA COMPRA-VENTA BONIFICACIONES';
                break;
            case static::HIDROCARBUROS_NO_IEHD:
                return 'FACTURA DE HIDROCARBUROS NO ALCANZADA IEHD';
                break;
            case static::SECTOR_EDUCATIVO_ZONA_FRANCA:
                return 'FACTURA SECTORES EDUCATIVO ZONA FRANCA';
                break;
            case static::ENGARRAFADORAS:
                return 'FACTURA ENGARRAFADORAS';
                break;
            
            
        }

    }

    public static function getInstancePdfByCode($code)
    {
   
    }
    
}
