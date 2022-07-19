<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResumeResource extends JsonResource
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
            "codigoProducto" => $this->resource['codigoProducto'],
            "descripcion" => $this->resource['descripcion'],
            "precioUnitario" => NumberUtils::number_format_custom( $this->resource['precioUnitario'], 2),
            "cantidad" => $this->resource['cantidad'],
            "montoDescuento" => NumberUtils::number_format_custom( $this->resource['montoDescuento'], 2),
            "subTotal" => NumberUtils::number_format_custom( $this->resource['subTotal'], 2),
        ];
    }
}
