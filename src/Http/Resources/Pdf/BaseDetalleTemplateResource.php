<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf;

use EmizorIpx\ClientFel\Models\Parametric\Unit;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseDetalleTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
                "codigoProducto" => isset($this->codigoProducto) ? $this->codigoProducto : '',
                "cantidad" => intval($this->cantidad) < $this->cantidad ? NumberUtils::number_format_custom( (float) $this->cantidad, 2) : NumberUtils::number_format_custom( (float) $this->cantidad, 2),
                "unidadDescripcion" => Unit::getUnitDescription($this->unidadMedida),
                "descripcion" => isset($this->descripcion) ? $this->descripcion : '',
                "precioUnitario" => intval($this->precioUnitario) < $this->precioUnitario ? NumberUtils::number_format_custom( (float) $this->precioUnitario, 2) : NumberUtils::number_format_custom( (float) $this->precioUnitario, 2),
                "montoDescuento" => isset($this->montoDescuento) ? NumberUtils::number_format_custom( (float) $this->montoDescuento, 2) : '0.00',
                "subTotal" => NumberUtils::number_format_custom( (float) $this->subTotal, 2)
    
            ];
    }
}
