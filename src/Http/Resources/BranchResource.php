<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            "id" => (int)$this->resource['id'],
            "codigo" => (int)$this->resource['codigo'],
            "descripcion" => $this->resource['descripcion']
        ];
    }
}