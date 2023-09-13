<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

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
        $obj = property_exists($obj_decoded, 'sobodaycom') ? $obj_decoded->sobodaycom : new stdClass;
        $concatenate = function ($x) use ($obj) {
            return isset($obj->{$x}) ?  
            collect($obj->{$x})->map(function ($d) {
                return $d->description;
            })->implode(",") : 
            "";
        };

        return [
            "num" => $this->resource->num,
            "numeroFactura" => $this->resource->numeroFactura,
            "sucursal" => $this->resource->codigoSucursal == 0 ? "Casa Matriz " : "Sucursal " . $this->resource->codigoSucursal,
            "numeroAutorizacion" => $this->resource->codigoSucursal.$this->resource->codigoPuntoVenta.$this->resource->numeroFactura,
            "numeroDocumento" => $this->resource->numeroDocumento,
            "nombreRazonSocial" => $this->resource->nombreRazonSocial,
            "eventoRubro" => $concatenate('eventos_rubros'),
            "lugarEvento" => $this->resource->client_name,
            "fechaEvento" => isset($obj->fecha_evento) ? $obj->fecha_evento : "",
            "artisasGrupos" => $concatenate('grupos_artistas'),
            "medioTransmision" => $concatenate('medios_transmisiones'),
            "importeTotal" => NumberUtils::number_format_custom($this->resource->montoTotal, 2),
        ];
    }
}
