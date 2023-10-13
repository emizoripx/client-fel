<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class UnpaidInvoicesResource extends JsonResource
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
            "numeroFactura" => $this->resource->numeroFactura,
            "fechaEmision" => $this->resource->fechaEmision,
            "nombreRazonSocial" => $this->resource->nombreRazonSocial,
            "numeroDocumento" => $this->resource->numeroDocumento,
            "cuf" => $this->resource->cuf,
            "montoTotal" => round((float)$this->resource->montoTotal, 2),
            "balance" => round((float)$this->resource->balance, 2),
        ];
    }
}
