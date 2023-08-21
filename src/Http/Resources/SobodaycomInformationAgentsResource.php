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

        $obj_decoded = json_decode($this->resource->extras);
        $obj = $obj_decoded->sobodaycom;
        $concatenate = function ($x) use ($obj) {
            return isset($obj->{$x}) ?  collect($obj->{$x})->map(function ($d) {
                return $d->description;
            })->implode(",") : "";
        };

        return [
            "num" => $this->resource->num,
            "numeroFactura" => $this->resource->numeroFactura,
            "numeroAutorización" => $this->resource->cuf,
            "numeroDocumento" => $this->resource->numeroDocumento,
            "nombreRazonSocial" => $this->resource->nombreRazonSocial,
            "eventoRubro" => isset($concatenate('eventos_rubros')) ? $concatenate('eventos_rubros') : "",
            "lugarEvento" => $this->resource->client_name,
            "fechaEvento" => isset($obj->fecha_evento) ? $obj->fecha_evento : "",
            "artisasGrupos" => isset($concatenate('grupos_artistas')) ? $concatenate('grupos_artistas') : "",
            "medioTransmision" => isset($concatenate('medios_transmisiones')) ? $concatenate('medios_transmisiones') : "",
            "importeTotal" => NumberUtils::number_format_custom($this->resource->montoTotal, 2),
        ];
    }
}
