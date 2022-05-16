<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\NotaConciliacion;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseDetalleTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class DetalleNotaConciliacionTemplateResource extends BaseDetalleTemplateResource {

    public function toArray($request)
    {

        $common = parent::toArray($request);

        return array_merge($common, [
            "montoOriginal" => isset($this->montoOriginal) ? NumberUtils::number_format_custom( (float) $this->montoOriginal, 2) : '',
            "montoFinal" => isset($this->montoFinal) ? NumberUtils::number_format_custom( (float) $this->montoFinal, 2) : '',
            "montoConciliado" => isset($this->montoOriginal) ? NumberUtils::number_format_custom( (float) $this->montoConciliado , 2) : '',
        ]);

    }

}