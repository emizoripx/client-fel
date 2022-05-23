<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaHidrocarburosAlcanzadosIehd;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleFacturaHidrocarburosAlcanzadosIehdTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        return array_merge( $common,
            [
                "porcentajeIehd" => $this->porcentajeIehd,
            ]
        );
        
    }

}