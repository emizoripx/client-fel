<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\AlcanzadaIce;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleAlcanzadaIceResource extends JsonResource
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
            "precioUnitario" => round($this->resource['precioUnitario'], 5),
            "alicuotaIva" => isset($this->resource['alicuotaIva']) ? round($this->resource['alicuotaIva'], 5) : null,
            "marcaIce" => isset($this->resource['marcaIce']) ? round($this->resource['marcaIce'], 5) : null,
            "precioNetoVentaIce" => isset($this->resource['precioNetoVentaIce']) ? round($this->resource['precioNetoVentaIce'], 5) : null,
            "alicuotaEspecifica" => isset($this->resource['alicuotaEspecifica']) ? round($this->resource['alicuotaEspecifica'], 5) : null,
            "alicuotaPorcentual" => isset($this->resource['alicuotaPorcentual']) ? round($this->resource['alicuotaPorcentual'], 5) : null,
            "montoIceEspecifico" => isset($this->resource['montoIceEspecifico']) ? round($this->resource['montoIceEspecifico'], 5) : null,
            "montoIcePorcentual" => isset($this->resource['montoIcePorcentual']) ? round($this->resource['montoIcePorcentual'], 5) : null,
            "cantidadIce" => isset($this->resource['cantidadIce']) ? round($this->resource['cantidadIce'], 5) : null,
            "subTotal" => round($this->resource['subTotal'], 5),
            "montoDescuento" => !empty($this->resource['montoDescuento']) ? round($this->resource['montoDescuento'], 5) : null,
            "unidadMedida" => $this->resource['unidadMedida'],
        ];
    }
}
