<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaPrevaloradaSdcf;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaPrevaloradaSdcfTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        return $common;
        
    }

}
