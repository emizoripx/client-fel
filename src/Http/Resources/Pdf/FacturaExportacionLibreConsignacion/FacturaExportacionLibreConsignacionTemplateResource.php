<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaExportacionLibreConsignacion;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaExportacionLibreConsignacionTemplateResource extends BaseTemplateResource {

    public function  toArray( $request ){

        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        return array_merge($common, [
            "title" => is_null($fel_invoice->cuf) ? "PREFACTURA COMERCIAL DE EXPORTACIÓN EN LIBRE CONSIGNACIÓN" : "FACTURA COMERCIAL DE EXPORTACIÓN EN LIBRE CONSIGNACIÓN",
            "subtitle" => is_null($fel_invoice->cuf) ? null : "(Sin Derecho A Crédito Fiscal)",
            "puertoDestino" => $fel_invoice->puertoDestino,
            "tipoCambio" => NumberUtils::number_format_custom( (float) $fel_invoice->tipoCambio, 2),
            "monedaDescripcion" => strtoupper(currency_description( $fel_invoice->codigoMoneda )),
            "montoTotalMoneda" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotalMoneda, 2 ),
            "montoTotalMonedaLiteral" => to_word((float)( $fel_invoice->montoTotalMoneda), 2, $fel_invoice->codigoMoneda),
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal, 2 ),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal), 2, 1) ,
            "detalles" => DetalleFacturaExportacionLibreConsignacionTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()
        ]);

    }

}