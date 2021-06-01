<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\ComercialExportacion;

use Illuminate\Http\Resources\Json\JsonResource;

class ComercialExportacionResource extends JsonResource
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
            "codigoPais" => $this->paisDestino,
            "incoterm" => $this->incoterm,
            "tipoCambioANB" => $this->tipoCambioANB,
            "numeroLote" => $this->numeroLote,
            "codigoMoneda" => $this->codigoMoneda,
            "montoTotalMoneda" => round($this->montoTotalMoneda, 2),
            "montoTotal" => round($this->montoTotal, 2),
            "montoTotalSujetoIva" => round($this->montoTotal, 2),
            "montoDescuento" => round($this->montoDescuento, 2),
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
            
            "montoDetalle" => round(collect($this->detalles)->sum('subTotal'),2),
            "incotermDetalle" =>  $this->incoterm_detalle,
            "totalGastosNacionalesFob" =>  round((float)$this->totalGastosNacionalesFob,2),
            "totalGastosInternacionales" => round((float)$this->totalGastosInternacionales,2),
            "numeroDescripcionPaquetesBultos" => (string) $this->numeroDescripcionPaquetesBultos,
            "informacionAdicional" => (string) $this->informacionAdicional,
            "lugarDestino" => (string) $this->lugarDestino,
            'detalles' => DetalleComercialExportacionResource::collection(collect($this->detalles)),
            "costosGastosNacionales" => CostosComercialExportacionResource::collection(collect($this->costosGastosNacionalesChanged)),
            "costosGastosInternacionales" => CostosComercialExportacionResource::collection(collect($this->costosGastosInternacionalesChanged)),
        ];
    }
}
