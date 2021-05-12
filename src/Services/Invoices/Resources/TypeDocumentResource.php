<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources;

use EmizorIpx\ClientFel\Services\Invoices\Resources\ComercialExportacion\ComercialExportacionResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\CompraVenta\CompraVentaResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\DebitoCredito\DebitoCreditoResource;
use EmizorIpx\ClientFel\Services\Invoices\Resources\ExportacionMinerales\ExportacionMineralesResource;
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
            case TypeDocumentSector::EXPORTACION_MINERALES:
                return new ExportacionMineralesResource($this);
                break;
            case TypeDocumentSector::VENTA_INTERNA_MINERALES:
                return new VentaMineralesResource($this);
                break;
            case TypeDocumentSector::COMERCIAL_EXPORTACION:
                return new ComercialExportacionResource($this);
                break;
            case TypeDocumentSector::DEBITO_CREDITO;
                return new DebitoCreditoResource($this);
            default:
                return new CompraVentaResource($this);
                break;
        }
    }
}
