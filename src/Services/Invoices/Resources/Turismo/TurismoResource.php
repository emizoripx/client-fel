<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\Turismo;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TurismoResource extends JsonResource
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
            'detalles' => DetalleTurismoResource::collection(collect($this->detalles)),
            "emailCliente" => $this->emailCliente,
            "cafc" => $this->cafc,
            "codigoExcepcion" => $this->codigoExcepcion,
            "montoGiftCard" => round($this->montoGiftCard,2),
            "descuentoAdicional" => round($this->descuentoAdicional,2),
            "cantidadHuespedes" => $this->cantidadHuespedes ,
            "cantidadHabitaciones" => $this->cantidadHabitaciones ,
            "cantidadMayores" => $this->cantidadMayores ,
            "cantidadMenores" => $this->cantidadMenores ,
            "fechaIngresoHospedaje" => substr(Carbon::parse($this->fechaIngresoHospedaje)->format('Y-m-d\TH:i:s.u'), 0, -3),
            "extras" => json_decode($this->extras),
            "razonSocialOperadorTurismo" => isset($this->data_specific_by_sector['razonSocialOperadorTurismo']) ? $this->data_specific_by_sector['razonSocialOperadorTurismo'] : '', 
        ];
    }
}
