<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaLubricantes;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaLubricantesTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        $common["subTotal"] = isset($fel_invoice->montoTotal) ? NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal + $fel_invoice->descuentoAdicional, 2) : '';

        return array_merge($common, [
            "title" => is_null($fel_invoice->cuf) ? "PREFACTURA" : "FACTURA",
            "subtitle" => is_null($fel_invoice->cuf) ? null : "(Con Derecho A Crédito Fiscal)",
            "montoTotalSujetoIva" => is_null($fel_invoice->cuf) ? null : NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotalSujetoIva ) , 2),
            "montoGiftCard" => NumberUtils::number_format_custom( (float) ( $fel_invoice->montoGiftCard) , 2),
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "totalPagar" => NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotal - $fel_invoice->montoGiftCard) , 2),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal - $fel_invoice->montoGiftCard), 2, 1),
            "detalles" => DetalleFacturaLubricantesTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve(),
            "currencyShortCode" => "Bs",
            "ciudad" => isset($fel_invoice->data_specific_by_sector['ciudad']) ? $fel_invoice->data_specific_by_sector['ciudad'] : '',
            "nombrePropietario" => isset($fel_invoice->data_specific_by_sector['nombrePropietario']) ? $fel_invoice->data_specific_by_sector['nombrePropietario'] : '',
            "nombreRepresentanteLegal" => isset($fel_invoice->data_specific_by_sector['nombreRepresentanteLegal']) ? $fel_invoice->data_specific_by_sector['nombreRepresentanteLegal'] : '',
            "condicionPago" => isset($fel_invoice->data_specific_by_sector['condicionPago']) ? $fel_invoice->data_specific_by_sector['condicionPago'] : '',
            "periodoEntrega" => isset($fel_invoice->data_specific_by_sector['periodoEntrega']) ? $fel_invoice->data_specific_by_sector['periodoEntrega'] : '',
            "montoDeduccionIehdDS25530" => isset($fel_invoice->data_specific_by_sector['montoDeduccionIehdDS25530']) ? NumberUtils::number_format_custom( (float) $fel_invoice->data_specific_by_sector['montoDeduccionIehdDS25530'], 2) : '0.00',
        ]);
        
    }

}