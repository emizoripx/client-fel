<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaComercializacionHidrocarburos;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaComercializacionHidrocarburosTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        return array_merge($common, [
            "title" => "FACTURA",
            "subtitle" => "(Con Derecho A Crédito Fiscal)",
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal - $fel_invoice->montoGiftCard), 2, 1) ,
            "montoTotalSujetoIvaLey317" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotalSujetoIvaLey317 , 2),
            "placaVehiculo" => $fel_invoice->placaVehiculo,
            "tipoEnvase" => $fel_invoice->tipoEnvase,
            "detalles" => DetalleFacturaComercializacionHidrocarburosTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()
        ]);

    }

}