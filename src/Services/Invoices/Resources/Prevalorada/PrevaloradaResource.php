<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\Prevalorada;

use Illuminate\Http\Resources\Json\JsonResource;

class PrevaloradaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        \Log::debug("datos " , [$this->codigoCliente,$this->nombreRazonSocial]);
        return [
            "codigoMoneda" => $this->codigoMoneda,
            "montoTotalMoneda" => round($this->montoTotalMoneda, 2),
            "montoTotal" => round($this->montoTotal, 2),
            "montoTotalSujetoIva" => round($this->montoTotalSujetoIva, 2),
            "numeroFactura" => $this->numeroFactura,
            "nombreRazonSocial" => "S/N",
            "codigoTipoDocumentoIdentidad" => 5,
            "numeroDocumento" => 0,
            "complemento" => null,
            "codigoCliente" => "N/A",
            "tipoCambio" => $this->tipoCambio,
            "codigoMetodoPago" => $this->codigoMetodoPago,
            "numeroTarjeta" => $this->numeroTarjeta,
            "codigoLeyenda" => $this->codigoLeyenda,
            "usuario" => $this->usuario,
            "codigoDocumentoSector" => $this->codigoDocumentoSector,
            "codigoPuntoVenta" => $this->codigoPuntoVenta,
            'detalles' => DetallePrevaloradaResource::collection(collect($this->detalles)),
            "emailCliente" => $this->emailCliente,
            "cafc" => $this->cafc,
            "montoGiftCard" => round($this->montoGiftCard,2),
            "extras" => json_decode($this->extras)
        ];
    }
}
