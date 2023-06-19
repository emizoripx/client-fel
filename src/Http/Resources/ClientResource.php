<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            "id" => $this->id,
            "id_origin" => $this->encodePrimaryKey($this->id),
            "company_id" => $this->encodePrimaryKey($this->company_id),
            "type_document_id" => (string) $this->type_document_id,
            "document_number" => $this->document_number,
            "complement" => $this->complement,
            "codigoExcepcion" => $this->codigoExcepcion,
            "business_name" => $this->business_name,
            "deleted_at" => $this->deleted_at,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
