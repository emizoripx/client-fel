<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaEntidadesFinancieras;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaEntidadedFinancierasTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        return array_merge($common, [
            "title" => is_null($fel_invoice->cuf) ? "PREFACTURA" : "FACTURA",
            "subtitle" => is_null($fel_invoice->cuf) ? null : "(Con Derecho A Crédito Fiscal)",
            "montoTotalSujetoIva" => is_null($fel_invoice->cuf) ? null : NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotalSujetoIva ) , 2),
            "montoTotalArrendamientoFinanciero" => isset($fel_invoice->data_specific_by_sector['montoTotalArrendamientoFinanciero']) ?  NumberUtils::number_format_custom( (float) ($fel_invoice->data_specific_by_sector['montoTotalArrendamientoFinanciero']) , 2): 0.00,
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "totalPagar" => NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotal - $fel_invoice->montoGiftCard) , 2),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal - $fel_invoice->montoGiftCard), 2, 1),
            "detalles" => DetalleFacturaCompraVentaBonificacionesTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve(),
            "currencyShortCode" => "Bs",
            "subTotal" => NumberUtils::number_format_custom((float) $fel_invoice->montoTotal - (isset($fel_invoice->data_specific_by_sector['montoTotalArrendamientoFinanciero']) ?  NumberUtils::number_format_custom((float) ($fel_invoice->data_specific_by_sector['montoTotalArrendamientoFinanciero']), 2) : 0.00) + $fel_invoice->descuentoAdicional, 2) ,
        ]);
        
    }

}