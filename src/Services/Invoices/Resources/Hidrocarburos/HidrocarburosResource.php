<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\Hidrocarburos;

use EmizorIpx\ClientFel\Services\Invoices\Resources\Hidrocarburos\DetalleHidrocarburosResource;
use Illuminate\Http\Resources\Json\JsonResource;

class HidrocarburosResource extends JsonResource
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
            "direccionComprador" => $this->direccionComprador,
            "ruex" => $this->ruex,
            "nim" => $this->nim,
            "concentradoGranel" => $this->concentradoGranel,
            "origen" => $this->origen,
            "puertoTransito" => $this->puertoTransito,
            "puertoDestino" => $this->puertoDestino,
            "paisDestino" => $this->paisDestino,
            "incoterm" => $this->incoterm,
            "tipoCambioANB" => $this->tipoCambioANB,
            "numeroLote" => $this->numeroLote,
            "fechaEmision"=> $this->fechaEmision,
            "codigoMoneda" => $this->codigoMoneda,
            "montoTotalMoneda" => round($this->montoTotalMoneda, 2),
            "montoTotal" => round($this->montoTotal, 2),
            "montoTotalSujetoIva" => round($this->montoTotalSujetoIva, 2),
            "montoDescuento" => round($this->montoDescuento, 2),
            "numeroFactura" => $this->numeroFactura,
            "montoIehd" => $this->montoIehd ? round($this->montoDescuento, 2): null,
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
            'detalles' => DetalleHidrocarburosResource::collection(collect($this->detalles)),
            "emailCliente" => $this->emailCliente,
            "cafc" => $this->cafc,
            "codigoExcepcion" => $this->codigoExcepcion,
            "ciudad" => $this->ciudad,
            "nombrePropietario" => $this->nombrePropietario,
            "nombreRepresentanteLegal" => $this->nombreRepresentanteLegal,
            "condicionPago" => $this->condicionPago,
            "periodoEntrega" => $this->periodoEntrega,
        ];
    }
}
