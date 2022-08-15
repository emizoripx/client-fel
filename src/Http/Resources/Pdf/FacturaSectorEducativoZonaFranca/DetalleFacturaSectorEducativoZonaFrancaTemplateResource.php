<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaSectorEducativoZonaFranca;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaSectorEducativoZonaFrancaTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        return $common;

    }

}