<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaSectoresEducativos;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaSectoresEducativosTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        return $common;

    }

}