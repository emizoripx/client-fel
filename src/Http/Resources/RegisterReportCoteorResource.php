<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\NumberUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterReportCoteorResource extends JsonResource
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
            "num" => $this->resource['num'],
            "codigoCliente" => $this->resource['codigoCliente'],
            "nombreRazonSocial" => $this->resource['nombreRazonSocial'],
            "numeroFactura" => $this->resource['numeroFactura'],
            "codigoProducto" => $this->resource['codigoProducto'],
            "subTotal" => NumberUtils::number_format_custom( $this->resource['subTotal'], 2),
            "fechaEmision" => $this->resource['fecha'],
            "nombreUsuario" => $this->resource['nombreUsuario'],
            "estado" => $this->resource['estado'],
            "cuf" => $this->resource['cuf'],
            "notaPublica" => $this->resource['public_notes'],
            "notaPrivada" => $this->resource['private_notes'],
        ];
    }
}
