<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class CuisResource extends JsonResource
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
            "cuis" => $this->cuis,
            "vigencia" => $this->validity,
            "codigoSistema" => $this->system_code,
            "company_id" => $this->company_id,
            "branch_code" => $this->branch_code,
            "pos_code" => $this->pos_code,
        ];
    }
}
