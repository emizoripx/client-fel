<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\NotaConciliacion;

use Illuminate\Http\Resources\Json\JsonResource;

class NotaConciliacionResource extends JsonResource
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
            "numeroNota" => $this->numeroFactura,
            "fechaEmision" => $this->fechaEmision,
            "nombreRazonSocial" => $this->nombreRazonSocial,
            "codigoTipoDocumentoIdentidad" => $this->codigoTipoDocumentoIdentidad,
            "numeroDocumento" => $this->numeroDocumento,
            "codigoCliente" => $this->codigoCliente,
            "numeroAutorizacionCuf" => $this->numeroAutorizacionCuf,
            "codigoLeyenda" => $this->codigoLeyenda,
            "usuario" => $this->usuario,
            "montoTotalDevuelto" => round($this->montoTotal, 2),
            "montoDescuentoCreditoDebito" => round($this->montoDescuentoCreditoDebito, 2),
            "montoEfectivoCreditoDebito" => round($this->montoEfectivoCreditoDebito, 2),
            'detalles' => DetalleNotaConciliacionResource::collection(collect($this->detalles)),
            "emailCliente" => $this->emailCliente,
            "codigoExcepcion" => $this->codigoExcepcion,
            "complemento" => $this->complemento,
        ];
    }
}
