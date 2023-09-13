<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class SobodaycomRegisterSalesResource extends JsonResource
{
    use MakesHash;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            "num" => $this->resource->num,
            "especificaciones" => $this->resource->especificaciones,
            "fechaEmision" => $this->resource->fechaEmision,
            "numeroFactura" => $this->resource->numeroFactura,
            "sucursal" => $this->resource->codigoSucursal == 0 ? "Casa Matriz ": "Sucursal ".$this->resource->codigoSucursal,
            "cuf" => $this->resource->cuf,
            "numeroDocumento" => $this->resource->numeroDocumento,
            "complemento" => $this->resource->complemento,
            "nombreRazonSocial" => $this->resource->nombreRazonSocial,
            "importeTotal" => NumberUtils::number_format_custom($this->resource->importeTotal, 2),
            "importeIce" => NumberUtils::number_format_custom($this->resource->importeIce, 2),
            "importeIEHD" => NumberUtils::number_format_custom($this->resource->importeIEHD, 2),
            "importeIPJ" => NumberUtils::number_format_custom($this->resource->importeIPJ, 2),
            "tasas" => NumberUtils::number_format_custom($this->resource->tasas, 2),
            "otros" => NumberUtils::number_format_custom($this->resource->otros, 2),
            "exportaciones" => NumberUtils::number_format_custom($this->resource->exportaciones, 2),
            "tasaCero" => NumberUtils::number_format_custom($this->resource->tasaCero, 2),
            "subTotal" => NumberUtils::number_format_custom($this->resource->subTotal, 2),
            "descuentoAdicional" => NumberUtils::number_format_custom($this->resource->descuentoAdicional, 2),
            "bonificaciones" => NumberUtils::number_format_custom($this->resource->bonificaciones, 2),
            "montoGiftCard" => NumberUtils::number_format_custom($this->resource->montoGiftCard, 2),
            "baseCreditoFiscal" => NumberUtils::number_format_custom($this->resource->baseCreditoFiscal, 2),
            "debitoFiscal" => NumberUtils::number_format_custom($this->resource->debitoFiscal, 2),
            "estado" => $this->resource->estado,
            "codigo_control" => $this->resource->codigo_control,
            "tipoVenta" => $this->resource->tipoVenta,
        ];
    }
}
