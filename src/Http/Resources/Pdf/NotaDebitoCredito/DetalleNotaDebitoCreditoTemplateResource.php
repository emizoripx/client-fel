<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\NotaDebitoCredito;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;

class DetalleNotaDebitoCreditoTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        return $common;

    }

}