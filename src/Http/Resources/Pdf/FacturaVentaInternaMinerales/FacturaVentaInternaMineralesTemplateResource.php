<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaVentaInternaMinerales;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaVentaInternaMineralesTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        $common['subTotal'] = NumberUtils::number_format_custom( (float) collect(json_decode(json_encode($fel_invoice->detalles)))->sum('subTotal'), 2);

        return array_merge($common, [
            "title" => is_null($fel_invoice->cuf) ? "PREFACTURA" : "FACTURA",
            "subtitle" => is_null($fel_invoice->cuf) ? null : "(Con Derecho a Crédito Fiscal)",
            "monedaDescripcion" => strtoupper(currency_description( $fel_invoice->codigoMoneda )),
            "direccionComprador" => $fel_invoice->direccionComprador,
            "concentradoGranel" => $fel_invoice->concentradoGranel,
            "origen" => $fel_invoice->origen,
            "puertoTransito" => $fel_invoice->puertoTransito,
            "incoterm" => $fel_invoice->incoterm,
            "puertoDestino" => $fel_invoice->puertoDestino,
            "destinyCountry" =>  $fel_invoice->paisDestino ? country($fel_invoice->paisDestino) : '---' ,
            "tipoCambio" => NumberUtils::number_format_custom( (float) $fel_invoice->tipoCambio, 2),
            "tipoCambioANB" => NumberUtils::number_format_custom( (float) $fel_invoice->tipoCambioANB, 2),
            "numeroLote" => $fel_invoice->numeroLote,
            "kilosNetosHumedos" => NumberUtils::number_format_custom( (float) $fel_invoice->kilosNetosHumedos, 2),
            "humedadPorcentaje" => NumberUtils::number_format_custom( (float) $fel_invoice->humedadPorcentaje, 2),
            "humedadValor" => NumberUtils::number_format_custom( (float) $fel_invoice->humedadValor, 2),
            "mermaPorcentaje" => NumberUtils::number_format_custom( (float) $fel_invoice->mermaPorcentaje,2),
            "mermaValor" => NumberUtils::number_format_custom( (float) $fel_invoice->mermaValor, 2),
            "kilosNetosSecos" => NumberUtils::number_format_custom( (float) $fel_invoice->kilosNetosSecos, 2),

            "gastosRealizacion" => NumberUtils::number_format_custom( (float) $fel_invoice->gastosRealizacion, 2 ),
            "iva" => NumberUtils::number_format_custom( (float) $fel_invoice->iva, 2 ),
            "liquidacionPreliminar" => NumberUtils::number_format_custom( (float) $fel_invoice->liquidacion_preliminar, 2 ),
            "montoTotalMoneda" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotalMoneda, 2 ),
            "montoTotalMonedaLiteral" => to_word( (float) $fel_invoice->montoTotalMoneda, 2, $fel_invoice->codigoMoneda),
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal, 2 ),
            "montoTotalLiteral" => to_word( (float) $fel_invoice->montoTotal, 2, 1) ,

            "detalles" => DetalleFacturaVentaInternaMineralesTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()
        ]);

    }

}