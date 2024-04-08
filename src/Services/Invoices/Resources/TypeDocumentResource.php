<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources;

use EmizorIpx\ClientFel\Services\Invoices\Resources\AlcanzadaIce\AlcanzadaIceResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Alquileres\AlquileresResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Clinicas\ClinicasResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ComercialConsignacion\ComercialConsignacionResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ComercialExportacion\ComercialExportacionResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ComercializacionGnv\ComercializacionGnvResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ComercializacionHidrocarburos\ComercializacionHidrocarburosResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\CompraVenta\CompraVentaResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\CompraVentaBonificaciones\CompraVentaBonificacionesResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\DebitoCredito\DebitoCreditoResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Engarrafadoras\EngarrafadorasResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\EntidadFinanciera\EntidadFinancieraResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ExportacionMinerales\ExportacionMineralesResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ExportacionServicios\ExportacionServiciosResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\HidrocarburosIehd\HidrocarburosIehdResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\HidrocarburosNoIehd\HidrocarburosNoIehdResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Hoteles\HotelesResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Lubricantes\LubricantesResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\NotaConciliacion\NotaConciliacionResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Prevalorada\PrevaloradaResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\PrevaloradaSdcf\PrevaloradaSdcfResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\SectorEducativo\SectorEducativoResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\SectorEducativoZonaFranca\SectorEducativoZonaFrancaResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Seguros\SegurosResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ServiciosBasicos\ServiciosBasicosResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Telecomunicaciones\TelecomunicacionesResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Turismo\TurismoResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\VentaMinerales\VentaMineralesResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ZonaFranca\ZonaFrancaResource;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use Illuminate\Http\Resources\Json\JsonResource;

class TypeDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        \Log::debug("========================= ANTES ======= ". $this->fechaEmision );
        $this->fechaEmision = substr((\Carbon\Carbon::parse($this->fechaEmision))->setTimezone('America/La_Paz')->format('Y-m-d\TH:i:s.u'), 0, -3) ;

        \Log::debug("========================= DESPUES ======= " . $this->fechaEmision);
        switch ($this->type_document_sector_id) {
            
            case TypeDocumentSector::COMPRA_VENTA:
                return new CompraVentaResource($this);
                break;
            case TypeDocumentSector::PREVALORADA:
                return new PrevaloradaResource($this);
                break;
            case TypeDocumentSector::ALQUILER_BIENES_INMUEBLES:
                return new AlquileresResource($this);
                break;
            case TypeDocumentSector::COMERCIAL_EXPORTACION_LIBRE_CONSIGNACION:
                return new ComercialConsignacionResource($this);
                break;
            case TypeDocumentSector::ZONA_FRANCA:
                return new ZonaFrancaResource($this);
                break;
            case TypeDocumentSector::EXPORTACION_MINERALES:
                return new ExportacionMineralesResource($this);
                break;
            case TypeDocumentSector::SECTORES_EDUCATIVOS:
                return new SectorEducativoResource($this);
                break;
            case TypeDocumentSector::COMERCIALIZACION_HIDROCARBUROS:
                return new ComercializacionHidrocarburosResource ($this);
                break;
            case TypeDocumentSector::COMERCIALIZACION_GNV:
                return new ComercializacionGnvResource ($this);
                break;
            case TypeDocumentSector::SERVICIOS_BASICOS:
                return new ServiciosBasicosResource($this);
                break;
            case TypeDocumentSector::HIDROCARBUROS_IEHD:
                return new HidrocarburosIehdResource($this);
                break;
            case TypeDocumentSector::VENTA_INTERNA_MINERALES:
                return new VentaMineralesResource($this);
                break;
            case TypeDocumentSector::COMERCIAL_EXPORTACION:
                return new ComercialExportacionResource($this);
                break;
            case TypeDocumentSector::TELECOMUNICACIONES:
                return new TelecomunicacionesResource($this);
                break;
            case TypeDocumentSector::HOTELES:
                return new HotelesResource($this);
                break;
            case TypeDocumentSector::HOSPITALES_CLINICAS :
                return new ClinicasResource($this);
                break;
            case TypeDocumentSector::DEBITO_CREDITO:
                return new DebitoCreditoResource($this);
                break;
            case TypeDocumentSector::COMERCIAL_EXPORTACION_SERVICIOS :
                return new ExportacionServiciosResource($this);
                break;
            case TypeDocumentSector::NOTA_CONCILIACION :
                return new NotaConciliacionResource($this);
                break;
            case TypeDocumentSector::SEGUROS:
                return new SegurosResource($this);
                break;
            case TypeDocumentSector::COMPRA_VENTA_BONIFICACIONES:
                return new CompraVentaBonificacionesResource($this);
                break;
            case TypeDocumentSector::HIDROCARBUROS_NO_IEHD :
                return new HidrocarburosNoIehdResource($this);
                break;
            case TypeDocumentSector::SECTOR_EDUCATIVO_ZONA_FRANCA:
                return new SectorEducativoZonaFrancaResource($this);

            case TypeDocumentSector::ENGARRAFADORAS:
                return new EngarrafadorasResource($this);

            case TypeDocumentSector::PRODUCTOS_ALCANZADOS_ICE:
                return new AlcanzadaIceResource($this);

            case TypeDocumentSector::SERVICIO_TURISTICO_HOSPEDAJE:
                return new TurismoResource($this);

            case TypeDocumentSector::PREVALORADA_SDCF:
                return new PrevaloradaSdcfResource($this);

            case TypeDocumentSector::ENTIDADES_FINANCIERAS:
                return new EntidadFinancieraResource($this);
            case TypeDocumentSector::LUBRICANTES:
                return new LubricantesResource($this);


            default:
                return new CompraVentaResource($this);
                break;
        }
    }
}
