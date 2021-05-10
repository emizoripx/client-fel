<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\ComercialExportacion;

use Illuminate\Http\Resources\Json\JsonResource;

class CostosComercialExportacionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "campo" => $this->resource['campo'],
            "valor" => $this->resource['valor']
        ];
    }
}
