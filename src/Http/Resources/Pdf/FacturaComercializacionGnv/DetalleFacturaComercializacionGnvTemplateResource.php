<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercializacionGnv;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaComercializacionGnvTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        return $common;

    }

}