<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources;

use EmizorIpx\ClientFel\Services\Invoices\Resources\ComercialConsignacion\ComercialConsignacionResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ComercialExportacion\ComercialExportacionResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\CompraVenta\CompraVentaResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\DebitoCredito\DebitoCreditoResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ExportacionMinerales\ExportacionMineralesResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Hidrocarburos\HidrocarburosResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Hoteles\HotelesResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\SectorEducativo\SectorEducativoResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ServiciosBasicos\ServiciosBasicosResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\Telecomunicaciones\TelecomunicacionesResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\VentaMinerales\VentaMineralesResource;
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
  
        switch ($this->type_document_sector_id) {
            case TypeDocumentSector::COMPRA_VENTA:
                return new CompraVentaResource($this);
                break;
            case TypeDocumentSector::COMERCIAL_EXPORTACION_LIBRE_CONSIGNACION:
                return new ComercialConsignacionResource($this);
                break;
            case TypeDocumentSector::EXPORTACION_MINERALES:
                return new ExportacionMineralesResource($this);
                break;
            case TypeDocumentSector::SECTORES_EDUCATIVOS:
                return new SectorEducativoResource($this);
                break;
            case TypeDocumentSector::SERVICIOS_BASICOS:
                return new ServiciosBasicosResource($this);
                break;
            case TypeDocumentSector::HIDROCARBUROS:
                return new HidrocarburosResource($this);
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
            case TypeDocumentSector::DEBITO_CREDITO;
                return new DebitoCreditoResource($this);
            default:
                return new CompraVentaResource($this);
                break;
        }
    }
}
