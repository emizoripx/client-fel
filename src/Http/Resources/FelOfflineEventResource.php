<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FelOfflineEventResource extends JsonResource
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
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'pos_id' => $this->pos_id,
            'cuis' => $this->cuis,
            'cufd' => $this->cufd,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'state' => $this->state,
            'proccessed_at' => $this->proccess_at,
            'errors' => $this->errors,
            'fel_errors' => $this->fel_errors
        ];
      
        return $result;
    }
}
