<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemInvoiceDailyMovementResource extends JsonResource
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
            "estado" => $this->resource['estado'],
            "codigoCliente" => $this->resource['codigoCliente'],
            "numeroDocumento" => $this->resource['numeroDocumento'],
            "nombreRazonSocial" => $this->resource['nombreRazonSocial'],
            "tipoPago" => $this->resource['tipoPago'],
            "fechaPago" => $this->resource['fechaPago'],
            "codigoProducto" => $this->resource['codigoProducto'],
            "descripcion" => $this->resource['descripcion'],
            "usuario" => $this->resource['usuario'],
            "subTotal" => $this->resource['estado'] != 'ANULADO' ?  round((float)$this->resource['montoTotal'] + (float)$this->resource['descuentoAdicional'], 2) :0,
            "descuentoAdicional" => round((float)$this->resource['descuentoAdicional'], 2),
            "montoTotal" => $this->resource['estado'] != 'ANULADO' ?  round((float)$this->resource['montoTotal'], 2) :0,
            "poliza" => $this->resource['poliza'],
            "agencia" => $this->resource['agencia'],
        ];
    }
}
