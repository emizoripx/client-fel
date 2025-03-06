<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemInvoiceQuipusReportResource extends JsonResource
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
            "numeroFactura" => $this->resource['numeroFactura'],
            "fechaEmision" => $this->resource['fechaEmision'],
            "estado" => $this->resource["estado"],
            "sucursal" => $this->resource["sucursal"],
            "nombreRazonSocial" => $this->resource['nombreRazonSocial'],
            "codigoTipoDocumentoIdentidad" => $this->resource['codigoTipoDocumentoIdentidad'],
            "numeroDocumento" => $this->resource['numeroDocumento'],
            "descripcion" => $this->resource['descripcion'],
            "precioUnitario" => $this->resource['precioUnitario'],
            "cantidad" => $this->resource['cantidad'],
            "descuentoAdicional" => round((float)$this->resource['descuentoAdicional'], 2),
            "subTotal" => round((float)$this->resource['montoTotal'] + (float)$this->resource['descuentoAdicional'], 2),
            "montoTotal" => round((float)$this->resource['montoTotal'], 2),
            "cuf" => $this->resource["cuf"],
            "usuario" => $this->resource['usuario'],
        ];
    }
}
