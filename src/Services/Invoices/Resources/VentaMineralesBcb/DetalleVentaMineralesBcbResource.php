<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\VentaMineralesBcb;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleVentaMineralesBcbResource extends JsonResource
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
            "descripcionLeyes" => $this->resource['descripcionLeyes'],
            "codigoProductoSin" => $this->resource['codigoProductoSin'],
            "codigoNandina" => $this->resource['codigoNandina'],
            "codigoProducto" => $this->resource['codigoProducto'],
            "codigoActividadSin" => $this->resource['codigoActividadEconomica'],
            "descripcion" => $this->resource['descripcion'],
            "cantidad" => $this->resource['cantidad'],
            "cantidadExtraccion" => $this->resource['cantidadExtraccion'],
            "precioUnitario" => $this->resource['precioUnitario'],
            "subTotal" => $this->resource['subTotal'],
            "unidadMedida" => $this->resource['unidadMedida'],
            "unidadMedidaExtraccion" => $this->resource['unidadMedidaExtraccion'],
        ];
    }
}
