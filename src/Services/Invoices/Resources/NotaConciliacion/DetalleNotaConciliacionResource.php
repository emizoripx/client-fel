<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\NotaConciliacion;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleNotaConciliacionResource extends JsonResource
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
            "subTotal" => $this->resource['montoFinal'],
            "unidadMedida" => $this->resource['unidadMedida'],
            "montoDescuento" => $this->resource['montoDescuento'] ?? null,
            "montoConciliado" => $this->resource["montoConciliado"],
            "montoFinal" => $this->resource["montoFinal"],
            "subtotalOriginal" => $this->resource["montoOriginal"],
        ];
    }
}
