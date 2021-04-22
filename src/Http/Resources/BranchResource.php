<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BranchResource extends ResourceCollection
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
            "id" => $this->encodePrimaryKey((int)$this->resource['id']),
            "codigo" => (int)$this->resource['codigo'],
            "descripcion" => $this->resource['descripcion'],
            "pos" => POSResource::collection($this->resource['fel_pos'])
        ];
    }
}