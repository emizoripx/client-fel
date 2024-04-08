<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaLubricantes;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaLubricantesTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        return array_merge( $common,
            [
                "cantidadLitros" => $this->cantidadLitros,
                "porcentajeDeduccionIehdDS25530" => $this->porcentajeDeduccionIehdDS25530,
            ]
        );
        
    }

}