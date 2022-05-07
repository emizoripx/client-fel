<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaTasaCero;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaTasaCeroTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        return $common;

    }

}