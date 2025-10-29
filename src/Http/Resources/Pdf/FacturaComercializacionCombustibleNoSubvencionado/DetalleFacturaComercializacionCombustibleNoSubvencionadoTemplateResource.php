<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercializacionCombustibleNoSubvencionado;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaComercializacionCombustibleNoSubvencionadoTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        return $common;

    }

}