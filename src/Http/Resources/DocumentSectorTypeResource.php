<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentSectorTypeResource extends JsonResource
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
            "id" => (int) $this->resource['id'],
            "codigo" => $this->resource['codigo'],
            "documentoSector" => $this->resource['documentoSector'],
            "tipoFactura" => $this->resource['tipoFactura'],
            "created_at" => $this->resource['created_at'],
            "updated_at" => $this->resource['updated_at']
        ];
    }
}
