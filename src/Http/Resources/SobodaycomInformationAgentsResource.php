<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class SobodaycomInformationAgentsResource extends JsonResource
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
            "num" => $this->resource['num'],
            "numeroFactura" => $this->resource['numeroFactura'],
            "numeroAutorización" => $this->resource['numeroAutorización'],
            "numeroDocumento" => $this->resource['numeroDocumento'],
            "nombreRazonSocial" => $this->resource['nombreRazonSocial'],
            "eventoRubro" => $this->resource['eventoRubro'],
            "lugarEvento" => $this->resource['lugarEvento'],
            "fechaEvento" => $this->resource['fechaEvento'],
            "artisasGrupos" => $this->resource['artisasGrupos'],
            "importeTotal" => NumberUtils::number_format_custom( $this->resource['montoTotal'], 2),
        ];
    }
}
