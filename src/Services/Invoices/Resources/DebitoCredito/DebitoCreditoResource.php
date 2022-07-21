<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\DebitoCredito;

use Illuminate\Http\Resources\Json\JsonResource;

class DebitoCreditoResource extends JsonResource
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
            "numeroFactura" => $this->external_invoice_data['numeroFacturaOriginal'],
            "facturaExterna" => $this->facturaExterna,
            "descuentoFacturaOriginal" => 0,
            "codigoControl" => "",
            "fechaFacturaOriginal" => $this->external_invoice_data['fechaEmisionOriginal'],
            "montoTotalFacturaOriginal" =>  round($this->external_invoice_data['montoTotalOriginal'], 2),
            'detalleFacturaOriginal' => DetalleDebitoCreditoOriginalResource::collection(collect($this->detalles['original'])),
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
            'detalles' => DetalleDebitoCreditoResource::collection(collect($this->detalles['debitado'])),
            "emailCliente" => $this->emailCliente,
            "codigoExcepcion" => $this->codigoExcepcion,
            "complemento" => $this->complemento,
            "extras" => json_decode($this->extras)
        ];
    }
}
