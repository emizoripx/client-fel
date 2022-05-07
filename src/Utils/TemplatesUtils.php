<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaAlquileres\FacturaAlquileresTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercialExportacion\FacturaComercialExportacionTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercialExportacionMinerales\FacturaComercialExportacionMineralesTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercialExportacionServicios\FacturaComercialExportacionServiciosTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercializacionHidrocarburos\FacturaComercializacionHidrocarburosTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaCompraVenta\FacturaCompraVentaTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaExportacionLibreConsignacion\FacturaExportacionLibreConsignacionTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaSectoresEducativos\FacturaSectoresEducativosTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaTasaCero\FacturaTasaCeroTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaTelecomunicaciones\FacturaTelecomunicacionesTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaVentaInternaMinerales\FacturaVentaInternaMineralesTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\NotaDebitoCredito\NotaDebitoCreditoTemplateResource;

class TemplatesUtils {

    public static function getClassResourceByDocumentSector ( $document_sector ) {

        switch ($document_sector) {
            case TypeDocumentSector::COMPRA_VENTA:
                return FacturaCompraVentaTemplateResource::class;
                break;
            case TypeDocumentSector::ALQUILER_BIENES_INMUEBLES:
                return FacturaAlquileresTemplateResource::class;
                break;
            case TypeDocumentSector::COMERCIAL_EXPORTACION:
                return FacturaComercialExportacionTemplateResource::class;
                break;
            case TypeDocumentSector::COMERCIAL_EXPORTACION_LIBRE_CONSIGNACION :
                return FacturaExportacionLibreConsignacionTemplateResource::class;
                break;
            case TypeDocumentSector::TASA_CERO :
                return FacturaTasaCeroTemplateResource::class;
                break;
            case TypeDocumentSector::SECTORES_EDUCATIVOS:
                return FacturaSectoresEducativosTemplateResource::class;
                break;
            case TypeDocumentSector::COMERCIALIZACION_HIDROCARBUROS :
                return FacturaComercializacionHidrocarburosTemplateResource::class;
                break;
            case TypeDocumentSector::EXPORTACION_MINERALES:
                return FacturaComercialExportacionMineralesTemplateResource::class;
                break;
            case TypeDocumentSector::VENTA_INTERNA_MINERALES:
                return FacturaVentaInternaMineralesTemplateResource::class;
                break;
            case TypeDocumentSector::TELECOMUNICACIONES :
                return FacturaTelecomunicacionesTemplateResource::class;
                break;
            case TypeDocumentSector::DEBITO_CREDITO :
                return NotaDebitoCreditoTemplateResource::class;
                break;
            case TypeDocumentSector::COMERCIAL_EXPORTACION_SERVICIOS:
                return FacturaComercialExportacionServiciosTemplateResource::class;
                break;
            
            default:
                return FacturaCompraVentaTemplateResource::class;
                break;
        }

    }

}