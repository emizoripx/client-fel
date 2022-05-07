<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaCompraVenta;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaCompraVentaTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        return $common;
        
    }

}