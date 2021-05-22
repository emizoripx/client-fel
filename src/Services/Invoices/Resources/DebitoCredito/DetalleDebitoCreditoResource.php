<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\DebitoCredito;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleDebitoCreditoResource extends JsonResource
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
            "subTotal" => $this->resource['subTotal'],
            "unidadMedida" => $this->resource['unidadMedida'],
            "montoDescuento" => $this->resource['montoDescuento'],
        ];
    }
}
