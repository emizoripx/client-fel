<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaTelecomunicaciones;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaTelecomunicacionesTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        return $common;

    }

}