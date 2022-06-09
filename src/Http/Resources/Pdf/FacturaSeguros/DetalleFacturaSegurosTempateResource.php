<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaSeguros;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaSegurosTempateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        return $common;
        
    }

}