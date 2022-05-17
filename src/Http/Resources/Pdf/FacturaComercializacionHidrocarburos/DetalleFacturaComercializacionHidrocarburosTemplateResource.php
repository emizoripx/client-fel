<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercializacionHidrocarburos;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaComercializacionHidrocarburosTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        return $common;

    }

}