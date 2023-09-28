<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemInvoiceBestSellerProductsResource extends JsonResource
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
            "counter" => $this->resource['contador'],
            "codigo" => $this->resource['codigo_producto'],
            "precio_unitario" => round($this->resource['costo_vendido'],2),
            "descripcion" => $this->resource['nombre_producto'],
            "cantidad" => intval($this->resource['cantidad_vendida']),
            "subtotal_efectivo" => round($this->resource['subtotal'],2),
        ];
    }
}
