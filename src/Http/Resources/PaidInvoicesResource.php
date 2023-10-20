<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class PaidInvoicesResource extends JsonResource
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
            "fechaEmision" => $this->resource->fechaEmision,
            "sucursal" => $this->resource->codigoSucursal == 0 ? "Casa Matriz" : "Sucursal " . $this->resource->codigoSucursal,
            "numeroFactura" => $this->resource->numeroFactura,
            "cuf" => $this->resource->cuf,
            "tipoPago" => $this->resource->tipoPago,
            "referenciaPago" => $this->resource->transaction_reference,
            "codigoCliente" => $this->resource->codigoCliente,
            "nombreRazonSocial" => $this->resource->nombreRazonSocial,
            "montoTotalPagado" => round((float)$this->resource->amount, 2),
        ];
    }
}
