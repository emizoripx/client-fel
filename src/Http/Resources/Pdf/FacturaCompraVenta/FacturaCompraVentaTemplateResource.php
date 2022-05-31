<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaCompraVenta;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaCompraVentaTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        return array_merge($common, [
            "title" => is_null($fel_invoice->cuf) ? "PREFACTURA" : "FACTURA",
            "subtitle" => is_null($fel_invoice->cuf) ? null : "(Con Derecho A Crédito Fiscal)",
            "montoTotalSujetoIva" => is_null($fel_invoice->cuf) ? null : NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotalSujetoIva ) , 2),
            "montoGiftCard" => NumberUtils::number_format_custom( (float) ( $fel_invoice->montoGiftCard) , 2),
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "totalPagar" => NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotal - $fel_invoice->montoGiftCard) , 2),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal - $fel_invoice->montoGiftCard), 2, 1),
            "detalles" => DetalleFacturaCompraVentaTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve(),
            "currencyShortCode" => "Bs",
            "tipoCambio" => isset($fel_invoice->tipoCambio) ? NumberUtils::number_format_custom( (float) $fel_invoice->tipoCambio, 2) : '',
            "montoTotalMoneda" => isset($fel_invoice->montoTotalMoneda) ? NumberUtils::number_format_custom( (float) $fel_invoice->montoTotalMoneda, 2) : '',
            "montoTotalMonedaLiteral" => to_word((float)( $fel_invoice->montoTotalMoneda), 2, $fel_invoice->codigoMoneda) ,
        ]);
        
    }

}