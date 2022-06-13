<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaPrevalorada;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaPrevaloradaTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        return $common;
        
    }

}