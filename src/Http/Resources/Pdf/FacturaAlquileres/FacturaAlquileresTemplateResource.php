<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaAlquileres;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaAlquileresTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        return array_merge( $common, [
            "title" => "FACTURA DE ALQUILERES DE BIENES INMUEBLES",
            "subtitle" => "(Con Derecho A Crédito Fiscal)",
            "montoTotalSujetoIva" => is_null($fel_invoice->cuf) ? null : NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotalSujetoIva ) , 2),
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "totalPagar" => NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotal - $fel_invoice->montoGiftCard) , 2),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal - $fel_invoice->montoGiftCard), 2, 1),
            "periodoFacturado" => $fel_invoice->periodoFacturado,
            "detalles" => DetalleFacturaAlquileresTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()
        ]);

    }

}