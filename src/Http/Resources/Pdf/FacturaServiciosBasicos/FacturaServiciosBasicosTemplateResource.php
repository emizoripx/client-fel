<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaServiciosBasicos;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaServiciosBasicosTemplateResource extends BaseTemplateResource {

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
            "montoTotalParcial" => isset($fel_invoice->montoTotal) ? NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal + $fel_invoice->descuentoAdicional, 2) : '0.00' ,
            "totalPagar" => isset($fel_invoice->montoTotal) ? NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotal - $fel_invoice->ajusteNoSujetoIva) , 2) : '0.00',
            "montoTotalLiteral" => to_word((float) ($fel_invoice->montoTotal - $fel_invoice->ajusteNoSujetoIva), 2, 1),
            "detalles" => DetalleFacturaServiciosBasicosTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve(),
            "currencyShortCode" => "Bs",
            "tipoCambio" => isset($fel_invoice->tipoCambio) ? NumberUtils::number_format_custom( (float) $fel_invoice->tipoCambio, 2) : '',
            "montoTotalMoneda" => isset($fel_invoice->montoTotalMoneda) ? NumberUtils::number_format_custom( (float) $fel_invoice->montoTotalMoneda, 2) : '',
            "montoDescuentoTarifaDignidad" => isset($fel_invoice->montoDescuentoTarifaDignidad) ? NumberUtils::number_format_custom( (float) $fel_invoice->montoDescuentoTarifaDignidad, 2) : '0.00',
            "detalleAjusteNoSujetoIva" => json_decode($fel_invoice->detalleAjusteNoSujetoIva),
            "detalleAjusteSujetoIva" => json_decode($fel_invoice->detalleAjusteSujetoIva),
            "detalleOtrosPagosNoSujetoIva" => json_decode($fel_invoice->detalleOtrosPagosNoSujetoIva),
            "ajusteNoSujetoIva" => isset($fel_invoice->ajusteNoSujetoIva) ? NumberUtils::number_format_custom( (float) $fel_invoice->ajusteNoSujetoIva, 2) : '0.00',
            "montoDescuentoLey1886" => isset($fel_invoice->montoDescuentoLey1886) ? NumberUtils::number_format_custom( (float) $fel_invoice->montoDescuentoLey1886, 2) : '0.00',
            "otrosPagosNoSujetoIva" => isset($fel_invoice->otrosPagosNoSujetoIva) ? NumberUtils::number_format_custom( (float) $fel_invoice->otrosPagosNoSujetoIva, 2) : '0.00',
            "ajusteSujetoIva" => isset($fel_invoice->ajusteSujetoIva) ? NumberUtils::number_format_custom( (float) $fel_invoice->ajusteSujetoIva, 2) : '',
            "otrasTasas" => isset($fel_invoice->otrasTasas) ? NumberUtils::number_format_custom( (float) $fel_invoice->otrasTasas, 2) : '0.00',
            "tasaAseo" => isset($fel_invoice->tasaAseo) ? NumberUtils::number_format_custom( (float) $fel_invoice->tasaAseo, 2) : '0.00',
            "tasaAlumbrado" => isset($fel_invoice->tasaAlumbrado) ? NumberUtils::number_format_custom( (float) $fel_invoice->tasaAlumbrado, 2) : '0.00',
            "tasasTotal" => NumberUtils::number_format_custom( (float) ($fel_invoice->tasaAlumbrado + $fel_invoice->tasaAseo + $fel_invoice->otrasTasas) , 2) ,
            "subtotalPagar" => NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotal) , 2) ,
            "domicilioCliente" => $fel_invoice->domicilioCliente,
            "beneficiarioLey1886" => $fel_invoice->beneficiarioLey1886,
            "consumoPeriodo" => $fel_invoice->consumoPeriodo,
            "mes" => $fel_invoice->mes,
            "gestion" => $fel_invoice->gestion,
            "numeroMedidor" => $fel_invoice->numeroMedidor,
        ]);
        
    }

}