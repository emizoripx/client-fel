<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\Lubricantes;

use Illuminate\Http\Resources\Json\JsonResource;

class LubricantesResource extends JsonResource
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
            "fechaEmision"=> $this->fechaEmision,
            "codigoMoneda" => $this->codigoMoneda,
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
            'detalles' => DetalleLubricantesResource::collection(collect($this->detalles)),
            "emailCliente" => $this->emailCliente,
            "cafc" => $this->cafc,
            "codigoExcepcion" => $this->codigoExcepcion,
            "ciudad" => isset($this->data_specific_by_sector['ciudad']) ? $this->data_specific_by_sector['ciudad'] : '',
            "nombrePropietario" => isset($this->data_specific_by_sector['nombrePropietario']) ? $this->data_specific_by_sector['nombrePropietario'] : '',
            "nombreRepresentanteLegal" => isset($this->data_specific_by_sector['nombreRepresentanteLegal']) ? $this->data_specific_by_sector['nombreRepresentanteLegal'] : '',
            "condicionPago" => isset($this->data_specific_by_sector['condicionPago']) ? $this->data_specific_by_sector['condicionPago'] : '',
            "periodoEntrega" => isset($this->data_specific_by_sector['periodoEntrega']) ? $this->data_specific_by_sector['periodoEntrega'] : '',
            "montoDeduccionIehdDS25530" => isset($this->data_specific_by_sector['montoDeduccionIehdDS25530']) ? round($this->data_specific_by_sector['montoDeduccionIehdDS25530'], 2) : '0.00',
            "descuentoAdicional" => round($this->descuentoAdicional,2),
            "extras" => json_decode($this->extras)
        ];
    }
}
