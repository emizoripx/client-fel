<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            "codigoNandina" => $this->codigo_nandina,
            "codigo_actividad_economica" => (string) $this->codigo_actividad_economica,
            "codigo_producto" => $this->codigo_producto,
            "codigo_producto_sin" => (string)$this->codigo_producto_sin,
            "codigo_unidad" => (string)$this->codigo_unidad,
            "nombre_unidad" => $this->nombre_unidad,
            "id_origin" => $this->encodePrimaryKey($this->id_origin),
            "company_id" => $this->company_id,
            "id" => (int)$this->id,
        ];
    }
}
