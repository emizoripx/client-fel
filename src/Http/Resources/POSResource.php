<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class POSResource extends JsonResource
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
            "id" => $this->resource['id'],
            "codigo" => $this->resource['codigo'],
            "descripcion" => $this->resource['descripcion'],
            "branch_id" => $this->resource['branch_id'],
            "company_id" => $this->resource['company_id']
        ];
    }
}
