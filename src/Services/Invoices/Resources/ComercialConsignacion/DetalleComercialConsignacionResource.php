<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\ComercialConsignacion;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleComercialConsignacionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "codigoProducto" => $this->resource['codigoProducto'],
            "codigoActividadSin" => $this->resource['codigoActividadEconomica'],
            "descripcion" => $this->resource['descripcion'],
            "codigoProductoSin" => $this->resource['codigoProductoSin'],
            "cantidad" => $this->resource['cantidad'],
            "precioUnitario" => $this->resource['precioUnitario'],
            "subTotal" => $this->resource['subTotal'],
            "unidadMedida" => $this->resource['unidadMedida'],
            "codigoNandina" => $this->resource['codigoNandina'],
            "montoDescuento" => $this->resource['montoDescuento'] ?? 0
        ];
    }
}
