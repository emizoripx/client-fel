<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaServiciosBasicos;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaServiciosBasicosTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        return $common;
        
    }

}