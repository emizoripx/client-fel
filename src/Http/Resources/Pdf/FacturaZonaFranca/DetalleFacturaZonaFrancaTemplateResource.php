<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaZonaFranca;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaZonaFrancaTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        return $common;
        
    }

}