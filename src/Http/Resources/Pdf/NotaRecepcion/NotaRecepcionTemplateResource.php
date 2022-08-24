<?php

namespace EmizorIpx\ClientFel\Http\Resources\Pdf\NotaRecepcion;

use EmizorIpx\ClientFel\Models\FelCaption;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class NotaRecepcionTemplateResource extends JsonResource
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

        $branch = null;
        if( !empty($fel_invoice->company_id) ){
            \Log::debug("Get Branch >>>>>>>>>>>>>>>>>>>> ");
            $branch = $fel_invoice->getBranchByCode();
        }


        $extras = [];

        $extras_aux = FelInvoiceRequest::forceToArrayExtras(FelInvoiceRequest::ensureIterable($fel_invoice->extras));
        \Log::debug("extras ========> " . json_encode($extras_aux));
        if (is_array($extras_aux))
            $extras = $extras_aux;


        return array_merge([
            "companyLogo" => isset($this->company) ? 'data:image/jpg;base64,' . base64_encode(file_get_contents($this->company->present()->logo())) : '',
            "logoEmizor" => 'https://s3.amazonaws.com/EMIZOR/Logo-Emizor-Sep-2019.png' ,
            "isUnipersonalCompany" => isset($this->company) ? boolval($this->company->company_detail->is_uniper) : '',
            "title" => "ORDEN DE RECEPCIÓN",
            "razonSocialEmisor" => isset($this->company->company_detail->business_name) ? $this->company->company_detail->business_name : '',
            "company_name" => isset($this->entity->company->settings->name) ? $this->entity->company->settings->name : '',
            "name" => isset($this->entity->company->settings->name) ? $this->entity->company->settings->name : '',
            "codigoSucursal" => isset( $fel_invoice->codigoSucursal ) ? $fel_invoice->codigoSucursal : '',
            "codigoPuntoVenta" => isset($fel_invoice->codigoPuntoVenta) ? $fel_invoice->codigoPuntoVenta : '',
            "direccion" => isset($branch) ? $branch->zona : '',
            "telefono" => isset($branch) ? $branch->telefono : '',
            "municipio" => isset($branch) ? $branch->municipio : '',
            "nitEmisor" => isset($this->company) ? $this->company->settings->id_number : '',
            "numeroFactura" => isset($fel_invoice->document_number) ? $fel_invoice->document_number : '',
            "fechaCreacionNotFormat" => $fel_invoice->created_at,
            "numeroDocumento" => isset($fel_invoice->numeroDocumento) ? $fel_invoice->numeroDocumento : '',
            "complemento" => isset($fel_invoice->complemento) ? $fel_invoice->complemento : '' ,
            "nombreRazonSocial" => isset($fel_invoice->nombreRazonSocial) ? $fel_invoice->nombreRazonSocial : '',
            "codigoCliente" => isset($fel_invoice->codigoCliente) ? $fel_invoice->codigoCliente : '',
            "subTotal" => isset($fel_invoice->montoTotal) ? NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal + $fel_invoice->descuentoAdicional , 2) : '',
            "descuentoAdicional" => isset($fel_invoice->descuentoAdicional) ? NumberUtils::number_format_custom( (float) $fel_invoice->descuentoAdicional , 2) : '0.00',
            "environmentCode" => isset($this->company) ? $this->company->company_detail->production : '',
            "status_code" => isset($fel_invoice->codigoEstado) ? $fel_invoice->codigoEstado : '',
            "terminos" => !empty($this->terms) ? $this->terms : null,
            "notasPublicas" => isset($this->public_notes) ? $this->public_notes : null,
            "piePagina" => !empty($this->footer) ? $this->footer : null,
            "displayBusinessName" => isset($this->company->company_detail->business_name) ? 1 : 0,
            "currencyShortCode" => "Bs",
            "codigoMoneda" => $fel_invoice->codigoMoneda,
            "usuario" => isset($fel_invoice->usuario) ? $fel_invoice->usuario : '',
            "clientName" => isset($this->client->name) ? $this->client->name : '',
            "montoDetalle" => NumberUtils::number_format_custom( (float) collect(json_decode(json_encode($fel_invoice->detalles)))->sum('subTotal') , 2) ,
            "montoTotalLiteral" => to_word((float)($fel_invoice->montoTotal), 2, 1),
            "montoTotal" => NumberUtils::number_format_custom( (float) $fel_invoice->montoTotal , 2),
            "detalles" => DetalleNotaRecepcionTemplateResource::collection(json_decode(json_encode($fel_invoice->detalles)))->resolve()

        ], $extras);
    }
}
