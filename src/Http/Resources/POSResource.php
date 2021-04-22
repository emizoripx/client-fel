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
            "id" => $this->encodePrimaryKey($this->resource['id']),
            "codigo" => $this->resource['codigo'],
            "descripcion" => $this->resource['descripcion'],
            "codigo_sucursal" => $this->resource['codigoSucursal'],
            "company_id" => $this->resource['company_id']
        ];
    }
}
