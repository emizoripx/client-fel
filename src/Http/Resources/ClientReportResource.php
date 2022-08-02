<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ClientReportResource extends JsonResource
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
            "codigoCliente" => $this->number,
            "nombre" => $this->name ,
            "tipoDocumento" => $this->type_document_id,
            "nombreRazonSocial" => $this->business_name,
            "email" => $this->email,
            "numeroDocumento" => $this->document_number . ' ' . $this->complement,
            "numeroTelefono" => $this->phone ,
            "fechaRegistro" => Carbon::parse($this->created_at)->toDateTimeString()
        ];
    }
}
