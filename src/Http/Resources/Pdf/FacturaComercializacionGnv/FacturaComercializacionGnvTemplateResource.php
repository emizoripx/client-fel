<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercializacionGnv;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaComercializacionGnvTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        return array_merge($common, [
            "title" => is_null($fel_invoice->cuf) ? "PREFACTURA" : "FACTURA",
            "subtitle" => is_null($fel_invoice->cuf) ? null : "(Con Derecho A Crédito Fiscal)",
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal - $fel_invoice->montoGiftCard), 2, 1) ,
            "montoTotalSujetoIva" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotalSujetoIva , 2),
            "placaVehiculo" => isset($fel_invoice->data_specific_by_sector['placaVehiculo']) ? $fel_invoice->data_specific_by_sector['placaVehiculo'] : '',
            "tipoEnvase" => isset($fel_invoice->data_specific_by_sector['tipoEnvase']) ? $fel_invoice->data_specific_by_sector['tipoEnvase'] : '',
            "detalles" => DetalleFacturaComercializacionGnvTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()
        ]);

    }

}