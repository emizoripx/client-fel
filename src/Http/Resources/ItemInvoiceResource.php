<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemInvoiceResource extends JsonResource
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
            "fechaEmision" => $this->resource['fechaEmision'],
            "numeroFactura" => $this->resource['numeroFactura'],
            "codigoSucursal" => $this->resource['codigoSucursal'] == 0 ? "Casa Matriz" : "Sucursal " . $this->resource['codigoSucursal'],
            "nombreRazonSocial" => $this->resource['nombreRazonSocial'],
            "numeroDocumento" => $this->resource['numeroDocumento'],
            "codigoProducto" => $this->resource['codigoProducto'],
            "descripcion" => $this->resource['descripcion'],
            "precioUnitario" => NumberUtils::number_format_custom( $this->resource['precioUnitario'], 2),
            "cantidad" => $this->resource['cantidad'],
            "montoDescuento" => isset($this->resource['montoDescuento']) ? NumberUtils::number_format_custom( $this->resource['montoDescuento'], 2) : 0.00,
            "subTotal" => NumberUtils::number_format_custom( $this->resource['subTotal'], 2),
        ];
    }
}
