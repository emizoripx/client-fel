<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\CompraVenta;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleCompraVentaResource extends JsonResource
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
            "cantidad" => $this->resource['cantidad'],
            "precioUnitario" => $this->resource['precioUnitario'],
            "subTotal" => round($this->resource['subTotal'], 2),
            "unidadMedida" => $this->resource['unidadMedida'],
        ];
    }
}
