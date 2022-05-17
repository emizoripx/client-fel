<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\NotaConciliacion;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class NotaConciliacionTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        return array_merge($common, [
            "title" => "NOTA DE CONCILIACIÓN",
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal - $fel_invoice->montoGiftCard), 2, 1),
            "creditoFiscalIva" => NumberUtils::number_format_custom( (float) $fel_invoice->creditoFiscalIva , 2),
            "detalles" => isset($fel_invoice->detalles['conciliado']) ? DetalleNotaConciliacionTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles['conciliado'])))->resolve() : DetalleNotaConciliacionTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()
        ]);

    }

}