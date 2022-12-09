<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemInraResumenResource extends JsonResource
{

    use MakesHash;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "area" => $this->resource['custom_value1'],
            "codigo_area" => $this->resource['custom_value2'],
            "denominacion" => $this->resource['product_key'],
            "descripcion" => $this->resource['notes'],
            "codigoProducto" => $this->resource['codigo_producto'],
            "numeroFactura" => $this->resource['numeroFactura'],
            "fechaEmision" => $this->resource['fechaEmision'],
            "nombreRazonSocial" => $this->resource['nombreRazonSocial'],
            "numeroDocumento" => $this->resource['numeroDocumento'],
            "cantidad" => $this->resource['quantity'],
            "precioUnitario" => round($this->resource['cost'], 2),
            "subTotal" => round((float)($this->resource['cost'] * $this->resource['quantity']), 2),
        ];
    }
}
