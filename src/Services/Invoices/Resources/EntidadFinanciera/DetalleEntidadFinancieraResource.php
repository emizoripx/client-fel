<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\EntidadFinanciera;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleEntidadFinancieraResource extends JsonResource
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
            "descripcion" => $this->resource['descripcion'],
            "codigoProductoSin" => $this->resource['codigoProductoSin'],
            "codigoActividadSin" => $this->resource['codigoActividadEconomica'],
            "cantidad" => $this->resource['cantidad'],
            "precioUnitario" => $this->resource['precioUnitario'],
            "subTotal" => round($this->resource['subTotal'], 5),
            "montoDescuento" => !empty($this->resource['montoDescuento']) ? round($this->resource['montoDescuento'], 5) : null,
            "unidadMedida" => $this->resource['unidadMedida'],
        ];
    }
}
