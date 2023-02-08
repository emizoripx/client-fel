<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaTurismo;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaTurismoTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request) {

        $common = parent::toArray($request);

        return array_merge($common, [
            "detalleHuespedes" => isset($this->detalleHuespedes) ? json_decode($this->detalleHuespedes, true) : null,
            "codigoTipoHabitacion" => isset($this->codigoTipoHabitacion) ? $this->codigoTipoHabitacion : null
        ]);

    }
    

}
