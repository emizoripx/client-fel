<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\NotaRecepcion;

use EmizorIpx\ClientFel\Models\Parametric\Unit;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleNotaRecepcionTemplateResource extends JsonResource
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
                "descripcion" => isset($this->descripcion) ? $this->descripcion : '',
                "rango" => isset($this->rango) ? $this->rango : '',
                "cantidadDevuelto" => isset($this->cantidadDevuelto) ? $this->cantidadDevuelto : '',
                "cantidadEntrega" => isset($this->cantidadEntrega) ? $this->cantidadEntrega : '',
                "cantidadVendido" => isset($this->cantidadVendido) ? $this->cantidadVendido : '',
                "precioUnitario" => intval($this->precioUnitario) < $this->precioUnitario ? NumberUtils::number_format_custom( (float) $this->precioUnitario, 2) : NumberUtils::number_format_custom( (float) $this->precioUnitario, 2),
                "subTotal" => NumberUtils::number_format_custom( (float) $this->subTotal, 2)
    
            ];
    }
}
