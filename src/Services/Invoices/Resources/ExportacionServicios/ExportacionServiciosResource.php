<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\ExportacionServicios;

use Illuminate\Http\Resources\Json\JsonResource;

class ExportacionServiciosResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "codigoPais" => $this->paisDestino,
            "lugarDestino" => $this->lugarDestino,
            "fechaEmision"=> $this->fechaEmision,
            "codigoMoneda" => $this->codigoMoneda,
            "direccionComprador" => $this->direccionComprador,
            "informacionAdicional" => $this->informacionAdicional,
            "montoTotalMoneda" => round($this->montoTotalMoneda, 2),
            "montoTotal" => round($this->montoTotal, 2),
            "montoTotalSujetoIva" => round($this->montoTotalSujetoIva, 2),
            "numeroFactura" => $this->numeroFactura,
            "nombreRazonSocial" => $this->nombreRazonSocial,
            "codigoTipoDocumentoIdentidad" => $this->codigoTipoDocumentoIdentidad,
            "numeroDocumento" => $this->numeroDocumento,
            "complemento" => $this->complemento,
            "codigoCliente" => $this->codigoCliente,
            "tipoCambio" => $this->tipoCambio,
            "codigoMetodoPago" => $this->codigoMetodoPago,
            "numeroTarjeta" => $this->numeroTarjeta,
            "codigoLeyenda" => $this->codigoLeyenda,
            "usuario" => $this->usuario,
            "codigoDocumentoSector" => $this->codigoDocumentoSector,
            "codigoPuntoVenta" => $this->codigoPuntoVenta,
            'detalles' => DetalleExportacionServiciosResource::collection(collect($this->detalles)),
            "emailCliente" => $this->emailCliente,
            "cafc" => $this->cafc,
            "codigoExcepcion" => $this->codigoExcepcion,
            "descuentoAdicional" => round($this->descuentoAdicional,2),
        ];
    }
}
