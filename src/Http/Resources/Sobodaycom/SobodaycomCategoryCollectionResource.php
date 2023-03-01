<?php

namespace EmizorIpx\ClientFel\Http\Resources\Sobodaycom;

use Illuminate\Http\Resources\Json\JsonResource;

class SobodaycomCategoryCollectionResource extends JsonResource
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
            'description' => $this->description,
            'type' => $this->type
        ];
      
        return $result;
    }
}
