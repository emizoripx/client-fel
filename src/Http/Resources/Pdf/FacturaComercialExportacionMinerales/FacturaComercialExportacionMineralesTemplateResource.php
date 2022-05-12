<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercialExportacionMinerales;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaComercialExportacionMineralesTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);
        
        $fel_invoice = $this->fel_invoice;

        $common['subTotal'] = NumberUtils::number_format_custom( (float) collect(json_decode(json_encode($fel_invoice->detalles)))->sum('subTotal'), 2);

        return array_merge($common, [
            "title" => is_null($fel_invoice->cuf) ? "PREFACTURA COMERCIAL DE EXPORTACIÓN" : "FACTURA EXPORTACIÓN",
            "subtitle" => is_null($fel_invoice->cuf) ? null : "(Sin Derecho a Crédito Fiscal)",
            "ruex" => $fel_invoice->ruex,
            "nim" => $fel_invoice->nim,
            "direccionComprador" => $fel_invoice->direccionComprador,
            "concentradoGranel" => $fel_invoice->concentradoGranel,
            "origen" => $fel_invoice->origen,
            "puertoTransito" => $fel_invoice->puertoTransito,
            "incoterm" => $fel_invoice->incoterm,
            "puertoDestino" => $fel_invoice->puertoDestino,
            "destinyCountry" => $fel_invoice->paisDestino ? country($fel_invoice->paisDestino) : '---',
            "monedaDescripcion" => strtoupper(currency_description( $fel_invoice->codigoMoneda )),
            "tipoCambio" => NumberUtils::number_format_custom( (float) $fel_invoice->tipoCambio, 2),
            "tipoCambioANB" => NumberUtils::number_format_custom( (float) $fel_invoice->tipoCambioANB, 2),
            "pesoBrutoKg" => isset($fel_invoice->pesoBrutoKg) ? NumberUtils::number_format_custom( (float) $fel_invoice->pesoBrutoKg, 2) : '',
            "pesoBrutoGr" => isset($fel_invoice->pesoBrutoGr) ? NumberUtils::number_format_custom( (float) $fel_invoice->pesoBrutoGr, 2) : '',
            "pesoNetoGr" => isset($fel_invoice->pesoNetoGr) ? NumberUtils::number_format_custom( (float) $fel_invoice->pesoNetoGr, 2) : '',
            "numeroLote" => $fel_invoice->numeroLote,
            "kilosNetosHumedos" => NumberUtils::number_format_custom( (float) $fel_invoice->kilosNetosHumedos, 2),
            "humedadPorcentaje" => NumberUtils::number_format_custom( (float) $fel_invoice->humedadPorcentaje, 2),
            "humedadValor" => NumberUtils::number_format_custom( (float) $fel_invoice->humedadValor, 2),
            "mermaPorcentaje" => NumberUtils::number_format_custom( (float) $fel_invoice->mermaPorcentaje,2),
            "mermaValor" => NumberUtils::number_format_custom( (float) $fel_invoice->mermaValor, 2),
            "kilosNetosSecos" => NumberUtils::number_format_custom( (float) $fel_invoice->kilosNetosSecos, 2),

            "subTotalLiteral" => to_word( (float) collect(json_decode(json_encode($fel_invoice->detalles)))->sum('subTotal'), 2, $fel_invoice->codigoMoneda),
            "subTotalBsLiteral" => to_word( (float) (collect(json_decode(json_encode($fel_invoice->detalles)))->sum('subTotal') * $fel_invoice->tipoCambio), 2, 1),
            "gastosRealizacion" => NumberUtils::number_format_custom( (float) $fel_invoice->gastosRealizacion, 2 ),
            "gastosRealizacionLiteral" => to_word( (float) $fel_invoice->gastosRealizacion * $fel_invoice->tipoCambio, 2, 1),
            "montoTotalMoneda" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotalMoneda, 2 ),
            "montoTotalMonedaLiteral" => to_word( (float) $fel_invoice->montoTotalMoneda, 2, $fel_invoice->codigoMoneda),
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal, 2 ),
            "montoTotalLiteral" => to_word( (float) $fel_invoice->montoTotal, 2, 1),
            "otrosDatos" => $fel_invoice->otrosDatos,
            "fleteInternoUSDLiteral" =>  to_word( (float) isset($fel_invoice->otrosDatos['fleteInternoUSD']) ? $fel_invoice->otrosDatos['fleteInternoUSD'] : 0 , 2, $fel_invoice->codigoMoneda ),
            "valorPlataLiteral" => to_word( (float) isset($fel_invoice->otrosDatos['valorPlata']) ? $fel_invoice->otrosDatos['valorPlata'] : 0 , 2, $fel_invoice->codigoMoneda ),

            "detalles" => DetalleFacturaComercialExportacionMineralesTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()
        ]);

    }

}