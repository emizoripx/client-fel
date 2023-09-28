<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemInvoiceProductReportResource extends JsonResource
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
            "counter" => $this->resource['counter'],
            "fechaEmision" => $this->resource['fechaEmision'],
            "numeroFactura" => $this->resource['numeroFactura'],
            "numeroDocumento" => $this->resource['numeroDocumento'],
            "nombreRazonSocial" => $this->resource['nombreRazonSocial'],
            "codigoCliente" => $this->resource['codigoCliente'],
            "usuario" => $this->resource['usuario'],
            "estado_pago" => $this->resource['estado_pago'],
            "codigoProducto" => $this->resource['codigoProducto'],
            "descripcion" => $this->resource['descripcion'],
            "cantidad" => $this->resource['cantidad'],
            "precioUnitario" => $this->resource['precioUnitario'],
            "subTotal" => round((float)$this->resource['montoTotal'] + (float)$this->resource['descuentoAdicional'], 2),
            "descuentoAdicional" => round((float)$this->resource['descuentoAdicional'], 2),
            "montoTotal" => round((float)$this->resource['montoTotal'], 2),
        ];
    }
}
