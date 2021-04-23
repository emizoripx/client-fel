<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
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
            "id" => (int)$this->resource['id'],
            "codigo" => (int)$this->resource['codigo'],
            "descripcion" => $this->resource['descripcion'],
            "pos" => POSResource::collection($this->resource['fel_pos']),
            "tipos-documento-sector" => DocumentSectorTypeResource::collection(FelParametric::index(TypeParametrics::TIPOS_DOCUMENTO_SECTOR, $this->resource['company_id']))
        ];
    }
}