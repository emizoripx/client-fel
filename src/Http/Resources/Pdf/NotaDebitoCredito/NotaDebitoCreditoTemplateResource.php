<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\NotaDebitoCredito;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class NotaDebitoCreditoTemplateResource extends BaseTemplateResource {

    public function toArray($request)
    {
        
        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        return array_merge( $common, [
            "title" => "NOTA CRÉDITO - DÉBITO",
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "montoTotalLiteral" => to_word( (float) $fel_invoice->montoTotal, 2, 1) ,
            "montoEfectivoCreditoDebito" => NumberUtils::number_format_custom( (float) $fel_invoice->montoEfectivoCreditoDebito, 2),
            "detalles" => isset($fel_invoice->detalles['debitado']) ? DetalleNotaDebitoCreditoTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles['debitado'])))->resolve() : DetalleNotaDebitoCreditoTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()
        ]);

    }

}