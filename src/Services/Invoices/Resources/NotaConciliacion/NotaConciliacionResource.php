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
            "numeroFactura"=> $this->external_invoice_data['numeroFacturaOriginal'],
            "facturaExterna"=>$this->facturaExterna,
            "numeroAutorizacionCuf"=> $this->numeroAutorizacionCuf,
            "fechaFacturaOriginal"=> $this->external_invoice_data['fechaEmisionOriginal'],
            "codigoControl"=> $this->external_invoice_data['codigoControl'],
            "montoTotalFacturaOriginal"=>  round($this->external_invoice_data['montoTotalOriginal'],2),
            'detalleFacturaOriginal' => DetalleNotaConciliacionOriginalResource::collection(collect($this->detalles['original'])),
            "nombreRazonSocial" => $this->nombreRazonSocial,
            "codigoTipoDocumentoIdentidad" => $this->codigoTipoDocumentoIdentidad,
            "numeroDocumento" => $this->numeroDocumento,
            "codigoCliente" => $this->codigoCliente,
            "codigoLeyenda" => $this->codigoLeyenda,
            "usuario" => $this->usuario,
            "emailCliente" => $this->emailCliente,
            "codigoExcepcion" => $this->codigoExcepcion,
            "complemento" => $this->complemento,
            'detalles' => DetalleNotaConciliacionResource::collection(collect($this->detalles['conciliado'])),
            "debitoFiscalIva" => $this->debitoFiscalIva,
            "creditoFiscalIva" => $this->creditoFiscalIva,
            "montoTotalConciliado" => round($this->montoTotal, 2),
            "numeroNota" => $this->numeroFactura,
            "extras" => json_decode($this->extras)
        ];
    }
}
