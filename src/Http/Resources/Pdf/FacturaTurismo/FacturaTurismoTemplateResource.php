<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\FacturaTurismo;

use EmizorIpx\ClientFel\Http\Resources\Pdf\BaseTemplateResource;
use EmizorIpx\ClientFel\Utils\NumberUtils;

class FacturaTurismoTemplateResource extends BaseTemplateResource {

    public function toArray($request) {

        $common = parent::toArray($request);

        $fel_invoice = $this->fel_invoice;

        $countries = \DB::table('fel_countries')->pluck('descripcion', 'codigo');
        $room_types = \DB::table('fel_room_types')->pluck('descripcion', 'codigo');


        return array_merge( $common, [
            "title" => is_null($fel_invoice->cuf) ? "PREFACTURA DE SERVICIO TURÍSTICO Y HOSPEDAJE" : "	FACTURA DE SERVICIO TURÍSTICO Y HOSPEDAJE",
            "subtitle" => is_null($fel_invoice->cuf) ? null : "(Sin Derecho A Crédito Fiscal)",
            "montoTotalSujetoIva" => is_null($fel_invoice->cuf) ? null : NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotalSujetoIva ) , 2),
            "montoGiftCard" => NumberUtils::number_format_custom( (float) ( $fel_invoice->montoGiftCard) , 2),
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "totalPagar" => NumberUtils::number_format_custom( (float) ($fel_invoice->montoTotal - $fel_invoice->montoGiftCard) , 2),
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal - $fel_invoice->montoGiftCard), 2, 1),
            "detalles" => DetalleFacturaTurismoTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve(),
            "currencyShortCode" => "Bs",
            "tipoCambio" => isset($fel_invoice->tipoCambio) ? NumberUtils::number_format_custom( (float) $fel_invoice->tipoCambio, 2) : '',
            "montoTotalMoneda" => isset($fel_invoice->montoTotalMoneda) ? NumberUtils::number_format_custom( (float) $fel_invoice->montoTotalMoneda, 2) : '',
            "montoTotalMonedaLiteral" => to_word((float)( $fel_invoice->montoTotalMoneda), 2, $fel_invoice->codigoMoneda) ,
            "cantidadHuespedes" => $this->cantidadHuespedes ?? 0,
            "cantidadMayores" => $this->cantidadMayores ?? 0,
            "cantidadMenores" => $this->cantidadMenores ?? 0,
            "cantidadHabitaciones" => $this->cantidadHabitaciones ?? 0,
            "fechaIngresoHospedaje" => $this->fechaIngresoHospedaje,
            "countries" => $countries,
            "room_types" => $room_types,
            "razonSocialOperadorTurismo" => isset($fel_invoice->data_specific_by_sector['razonSocialOperadorTurismo']) ? $fel_invoice->data_specific_by_sector['razonSocialOperadorTurismo'] : null, 
        ]);

    }


}
