<?php

namespace EmizorIpx\ClientFel\Http\Resources\Agencies;

use Illuminate\Http\Resources\Json\JsonResource;

class AgencyCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'nit' => $this->nit,
            'email' => $this->email
        ];
      
        return $result;
    }
}
