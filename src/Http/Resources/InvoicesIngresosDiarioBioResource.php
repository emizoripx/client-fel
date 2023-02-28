<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoicesIngresosDiarioBioResource extends JsonResource
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
            "fechaEmision" => $this->fechaEmision,
            "nombreRazonSocial" => $this->nombreRazonSocial,
            "nombrePaciente" => $this->nombreRazonSocial,
            "montoTotal" => NumberUtils::format($this->montoTotal),
            "montoPagado" => NumberUtils::format($this->montoPagado),
            "numeroFactura" => $this->numeroFactura,
            "codigoOrden" => str_replace('"','', $this->codigoOrden),
            "empresa" => str_replace('"','', $this->empresa),
            "tipoPago" => $this->tipoPago == 'POR COBRAR' ? strtoupper($this->tipoPago) :  strtoupper(__('texts.payment_type_'.$this->tipoPago))
        ];
    }
}
