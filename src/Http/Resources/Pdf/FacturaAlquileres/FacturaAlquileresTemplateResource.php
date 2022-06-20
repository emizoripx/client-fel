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
            "title" => is_null($fel_invoice->cuf) ? "PREFACTURA DE ALQUILER" : "FACTURA DE ALQUILER",
            "subtitle" => is_null($fel_invoice->cuf) ? null :  "(Con Derecho A Crédito Fiscal)",
            "montoTotalSujetoIva" => is_null($fel_invoice->cuf) ? null : NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotalSujetoIva ) , 2),
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "totalPagar" => NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotal - $fel_invoice->montoGiftCard) , 2),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal - $fel_invoice->montoGiftCard), 2, 1),
            "periodoFacturado" => $fel_invoice->periodoFacturado,
            "codigoMoneda" => $this->codigoMoneda,
            "monedaDescripcion" => strtoupper(currency_description($fel_invoice->codigoMoneda)),
            "montoTotalMoneda" => NumberUtils::number_format_custom((float) $fel_invoice->montoTotalMoneda, 2),
            "detalles" => DetalleFacturaAlquileresTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()
        ]);

    }

}