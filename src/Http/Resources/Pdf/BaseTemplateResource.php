<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf;

use EmizorIpx\ClientFel\Models\FelCaption;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $fel_invoice = $this->fel_invoice;
        $branch = $fel_invoice->getBranchByCode();

        $extras = [];

        $extras_aux = json_decode($fel_invoice->extras);
        if (is_array($extras_aux))
            $extras = $extras_aux;


        return array_merge([
            "companyLogo" =>  'data:image/jpg;base64,' . base64_encode(file_get_contents($this->company->present()->logo())),
            "logoEmizor" => 'https://s3.amazonaws.com/EMIZOR/Logo-Emizor-Sep-2019.png' ,
            "isUnipersonalCompany" => boolval($this->company->company_detail->is_uniper),
            "razonSocialEmisor" => $this->company->company_detail->business_name,
            "codigoSucursal" => $fel_invoice->codigoSucursal,
            "codigoPuntoVenta" => $fel_invoice->codigoPuntoVenta,
            "direccion" => $branch->zona,
            "telefono" => $branch->telefono,
            "municipio" => $branch->municipio,
            "nitEmisor" => $this->company->settings->id_number,
            "numeroFactura" => $fel_invoice->numeroFactura,
            "cuf" => is_null($fel_invoice->cuf) ? null : $fel_invoice->cuf ,
            "fechaEmision" => $fel_invoice->getFechaEmisionFormated(),
            "numeroDocumento" => $fel_invoice->numeroDocumento,
            "complemento" => $fel_invoice->complemento,
            "nombreRazonSocial" => $fel_invoice->nombreRazonSocial,
            "codigoCliente" => $fel_invoice->codigoCliente,
            "subTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal + $fel_invoice->descuentoAdicional , 2),
            "descuentoAdicional" => NumberUtils::number_format_custom( (float) $fel_invoice->descuentoAdicional , 2),
            "leyendaSIN" => FelCaption::CAPTION_SIN,
            "leyenda" => FelCaption::getCaptionDescription($fel_invoice->codigoLeyenda),
            "leyendaSIN2" => $fel_invoice->getLeyendaEmissionType(),
            "qrCode" => \QrCode::generate($fel_invoice->getUrlSin()),
            "environmentCode" => $this->company->company_detail->production,
            "status_code" => $fel_invoice->codigoEstado,
            "terminos" => !empty($this->terms) ? $this->terms : null,
            "notasPublicas" => isset($this->public_notes) ? $this->public_notes : null,
            "piePagina" => !empty($this->footer) ? $this->footer : null,
            "paymnetQr" => $this->getPaymentQR(),
            "displayBusinessName" => isset($this->company->company_detail->displayBusinessName) ? $this->company->company_detail->displayBusinessName : null,
            "currencyShortCode" => "Bs",

        ], $extras);
    }
}
