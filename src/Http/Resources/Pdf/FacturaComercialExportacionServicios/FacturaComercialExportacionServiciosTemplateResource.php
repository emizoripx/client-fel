<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercialExportacionServicios;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaComercialExportacionServiciosTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);
        
        $fel_invoice = $this->fel_invoice;

        $common['subTotal'] = NumberUtils::number_format_custom( (float) collect(json_decode(json_encode($fel_invoice->detalles)))->sum('subTotal'), 2);


        return array_merge( $common, [
            "title" => "FACTURA COMERCIAL DE EXPORTACION DE SERVICIOS <br> (COMMERCIAL SERVICE EXPORT INVOICE)",
            "subtitle" => "(Sin Derecho A Crédito Fiscal)",
            "tipoCambio" => NumberUtils::number_format_custom( (float) $fel_invoice->tipoCambio, 2),
            "direccionComprador" => $fel_invoice->direccionComprador,
            "lugarDestino" => $fel_invoice->lugarDestino,
            "informacionAdicional" => $fel_invoice->informacionAdicional,
            "monedaDescripcion" => strtoupper(currency_description( $fel_invoice->codigoMoneda )),
            "montoTotalMoneda" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotalMoneda, 2 ),
            "montoTotalMonedaLiteral" => to_word((float)( $fel_invoice->montoTotalMoneda), 2, $fel_invoice->codigoMoneda),
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal, 2 ),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal), 2, 1),
            "detalles" => DetalleFacturaComercialExportacionServiciosTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()

        ]);

    }

}