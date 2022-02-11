<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\AlcanzadosIce;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleAlcanzadosIceResource extends JsonResource
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
            "subTotal" => round($this->resource['subTotal'], 2),
            "montoDescuento" => !empty($this->resource['montoDescuento']) ? round($this->resource['montoDescuento'], 2) : null,
            "unidadMedida" => $this->resource['unidadMedida'],
            "marcaIce" => $this->resource['marcaIce'],
            "alicuotaIva" => $this->resource['alicuotaIva'],
            "precioNetoVentaIce" => $this->resource['precioNetoVentaIce'],
            "alicuotaEspecifica" => $this->resource['alicuotaEspecifica'],
            "alicuotaPorcentual" => $this->resource['alicuotaPorcentual'],
            "montoIceEspecifico" => $this->resource['montoIceEspecifico'],
            "montoIcePorcentual" => $this->resource['montoIcePorcentual'],
            "cantidadIce" => $this->resource['cantidadIce'],
        ];
    }
}
