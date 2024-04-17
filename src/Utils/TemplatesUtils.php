<?php

namespace EmizorIpx\ClientFel\Utils;

use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaAlcanzadaIce\FacturaAlcanzadaIceTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaAlquileres\FacturaAlquileresTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaClinicas\FacturaClinicasTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercialExportacion\FacturaComercialExportacionTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercialExportacionMinerales\FacturaComercialExportacionMineralesTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercialExportacionServicios\FacturaComercialExportacionServiciosTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercializacionGnv\FacturaComercializacionGnvTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercializacionHidrocarburos\FacturaComercializacionHidrocarburosTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaCompraVenta\FacturaCompraVentaTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaCompraVentaBonificaciones\FacturaCompraVentaBonificacionesTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaEngarrafadoras\FacturaEngarrafadorasTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaEntidadesFinancieras\FacturaEntidadesFinancierasTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaExportacionLibreConsignacion\FacturaExportacionLibreConsignacionTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaHidrocarburosAlcanzadosIehd\FacturaHidrocarburosAlcanzadosIehdTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaHidrocarburosNoAlcanzadosIehd\FacturaHidrocarburosNoAlcanzadosIehdTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaHoteles\FacturaHotelesTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaLubricantes\FacturaLubricantesTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaPrevalorada\FacturaPrevaloradaTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaPrevaloradaSdcf\FacturaPrevaloradaSdcfTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaSectorEducativoZonaFranca\FacturaSectorEducativoZonaFrancaTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaSectoresEducativos\FacturaSectoresEducativosTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaSeguros\FacturaSegurosTempateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaServiciosBasicos\FacturaServiciosBasicosTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaTasaCero\FacturaTasaCeroTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaTelecomunicaciones\FacturaTelecomunicacionesTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaTurismo\FacturaTurismoTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaVentaInternaMinerales\FacturaVentaInternaMineralesTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaZonaFranca\FacturaZonaFrancaTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\NotaConciliacion\NotaConciliacionTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\NotaDebitoCredito\NotaDebitoCreditoTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\NotaEntrega\NotaEntregaTemplateResource;
use EmizorIpx\ClientFel\Http\Resources\Pdf\NotaRecepcion\NotaRecepcionTemplateResource;

class TemplatesUtils {

    public static function getClassResourceByDocumentSector ( $document_sector, $typeDocument = null ) {

        switch ($document_sector) {
            case TypeDocumentSector::COMPRA_VENTA:
                if ( $typeDocument == Documents::NOTA_ENTREGA  ) {
                    return NotaEntregaTemplateResource::class;
                }

                if ( $typeDocument == Documents::NOTA_RECEPCION  ) {
                    return NotaRecepcionTemplateResource::class;
                }
                
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
            case TypeDocumentSector::ZONA_FRANCA :
                return FacturaZonaFrancaTemplateResource::class;
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
            case TypeDocumentSector::COMERCIALIZACION_GNV :
                return FacturaComercializacionGnvTemplateResource::class;
                break;
            case TypeDocumentSector::SERVICIOS_BASICOS :
                return FacturaServiciosBasicosTemplateResource::class;
                break;
            case TypeDocumentSector::HOSPITALES_CLINICAS  :
                return FacturaClinicasTemplateResource::class;
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
            case TypeDocumentSector::PREVALORADA:
                return FacturaPrevaloradaTemplateResource::class;
                break;
            case TypeDocumentSector::DEBITO_CREDITO :
                return NotaDebitoCreditoTemplateResource::class;
                break;
            case TypeDocumentSector::COMERCIAL_EXPORTACION_SERVICIOS:
                return FacturaComercialExportacionServiciosTemplateResource::class;
                break;
            case TypeDocumentSector::NOTA_CONCILIACION:
                return NotaConciliacionTemplateResource::class;
                break;
            case TypeDocumentSector::HIDROCARBUROS_IEHD:
                return FacturaHidrocarburosAlcanzadosIehdTemplateResource::class;
                break;
            case TypeDocumentSector::HIDROCARBUROS_NO_IEHD:
                return FacturaHidrocarburosNoAlcanzadosIehdTemplateResource::class;
                break;
            case TypeDocumentSector::SEGUROS :
                return FacturaSegurosTempateResource::class;
            case TypeDocumentSector::COMPRA_VENTA_BONIFICACIONES:
                return FacturaCompraVentaBonificacionesTemplateResource::class;
            
            case TypeDocumentSector::SECTOR_EDUCATIVO_ZONA_FRANCA:
                return FacturaSectorEducativoZonaFrancaTemplateResource::class;
                break;
            case TypeDocumentSector::ENGARRAFADORAS:
                return FacturaEngarrafadorasTemplateResource::class;
                break;
            case TypeDocumentSector::PRODUCTOS_ALCANZADOS_ICE:
                return FacturaAlcanzadaIceTemplateResource::class;
                break;
            case TypeDocumentSector::HOTELES:
                return FacturaHotelesTemplateResource::class;
                break;
            case TypeDocumentSector::SERVICIO_TURISTICO_HOSPEDAJE:
                return FacturaTurismoTemplateResource::class;
                break;
            case TypeDocumentSector::PREVALORADA_SDCF:
                return FacturaPrevaloradaSdcfTemplateResource::class;
                break;
            case TypeDocumentSector::ENTIDADES_FINANCIERAS:
                return FacturaEntidadesFinancierasTemplateResource::class;
                break;
            case TypeDocumentSector::LUBRICANTES:
                return FacturaLubricantesTemplateResource::class;
                break;



            default:
                return FacturaCompraVentaTemplateResource::class;
                break;
        }

    }

}
