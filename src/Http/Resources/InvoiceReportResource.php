<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceReportResource extends JsonResource
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
            "fechaEmision" => $this->fechaEmision,
            "numeroFactura" => $this->numeroFactura,
            "cuf" => $this->cuf,
            "codigoSucursal" => $this->codigoSucursal,
            "estado" => $this->estado,
            "numeroDocumento" => $this->numeroDocumento,
            "nombreRazonSocial" => $this->nombreRazonSocial,
            "montoTotalVenta" => $this->montoTotalVenta,
            "descuentoAdicional" => $this->descuentoAdicional,
            "montoTotal" => $this->montoTotal,
        ];
    }
}
