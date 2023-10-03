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
            "nombreRazonSocial" => in_array($this->resource->codigoEstado, [905, 691]) ?"ANULADO": $this->resource->nombreRazonSocial,
            "eventoRubro" => in_array($this->resource->codigoEstado, [905, 691]) ? "ANULADO" : $concatenate('eventos_rubros'),
            "lugarEvento" => in_array($this->resource->codigoEstado, [905, 691]) ? "ANULADO" : $this->resource->client_name,
            "fechaEvento" =>  in_array($this->resource->codigoEstado, [905, 691]) ? "ANULADO" :( isset($obj->fecha_evento) ? $obj->fecha_evento : ""),
            "artisasGrupos" => in_array($this->resource->codigoEstado, [905, 691]) ? "ANULADO" : $concatenate('grupos_artistas'),
            "medioTransmision" => in_array($this->resource->codigoEstado, [905, 691]) ? "ANULADO" : $concatenate('medios_transmisiones'),
            "importeTotal" => in_array($this->resource->codigoEstado, [905, 691]) ? 0 :( NumberUtils::number_format_custom($this->resource->montoTotal, 2)),
        ];
    }
}
