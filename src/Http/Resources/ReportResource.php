<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use EmizorIpx\ClientFel\Models\FelReportRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "entity" => $this->entity,
            "company_id" => $this->company_id,
            "status" => FelReportRequest::getStatusDescripcion($this->status),
            "filepath" => isset($this->s3_filepath) ? Storage::disk('s3')->url($this->s3_filepath) : null,
            "report_date" => $this->report_date,
            "registered_at" => $this->registered_at,
            "start_process_at" => $this->start_process_at,
            "completed_at" => $this->completed_at,
            "user" => $this->first_name . ' ' . $this->last_name,
            "user_id" => $this->user_id,
            "from_date" => $this->from_date,
            "to_date" => $this->to_date,
        ];
    }
}
