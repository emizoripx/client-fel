<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercialExportacion;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaComercialExportacionTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        $array_costosGastosNacionales = [];
        foreach ($fel_invoice->costosGastosNacionales as $key => $value) {
            array_push($array_costosGastosNacionales, ["campo" => $key, "valor" => NumberUtils::number_format_custom( (float)($value), 2)]);
        }
        
        \Log::debug([$array_costosGastosNacionales]);
        $array_costosGastosInternacionales = [];

        foreach ($fel_invoice->costosGastosInternacionales as $key => $value) {
            array_push($array_costosGastosInternacionales, ["campo" => $key, "valor" => NumberUtils::number_format_custom( (float)($value), 2)]);
        }

        return array_merge( $common, [
            "title" => is_null($fel_invoice->cuf) ? "PREFACTURA COMERCIAL EXPORTACIÓN <br> (COMMERCIAL INVOICE)" : "FACTURA COMERCIAL EXPORTACIÓN <br> (COMMERCIAL INVOICE)",
            "subtitle" => is_null($fel_invoice->cuf) ? null : "(Sin Derecho a Crédito Fiscal)",
            "incoterm" => $fel_invoice->incoterm,
            "incotermDetalle" => $fel_invoice->incoterm_detalle,
            "direccionComprador" => $fel_invoice->direccionComprador,
            "puertoDestino" => $fel_invoice->puertoDestino,
            "paisDestino" => $fel_invoice->paisDestino ? country($fel_invoice->paisDestino) : '',
            "monedaDescripcion" => strtoupper($fel_invoice->getExchangeDescription()),
            "tipoCambio" => NumberUtils::number_format_custom( (float) $fel_invoice->tipoCambio, 2),
            "montoDetalle" => NumberUtils::number_format_custom( (float) collect(json_decode(json_encode($fel_invoice->detalles)))->sum('subTotal') , 2) ,
            "costosGastosNacionales" => $array_costosGastosNacionales,
            "costosGastosInternacionales" => $array_costosGastosInternacionales,
            "totalGastosNacionalesFob" => NumberUtils::number_format_custom( (float) $fel_invoice->totalGastosNacionalesFob , 2) ,
            "totalGastosInternacionales" => NumberUtils::number_format_custom( (float) $fel_invoice->totalGastosInternacionales , 2) ,
            "montoTotalMonedaLiteral" => to_word((float)( $fel_invoice->montoTotalMoneda), 2, $fel_invoice->codigoMoneda),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal), 2, 1),
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "montoTotalMoneda" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotalMoneda , 2),
            "numeroDescripcionPaquetesBultos" => $fel_invoice->numeroDescripcionPaquetesBultos,
            "informacionAdicional" => $fel_invoice->informacionAdicional,
            "detalles" => DetalleFacturaComercialExportacionTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()
        ]);

    }

}