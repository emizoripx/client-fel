<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\ExportacionMinerales;

use Illuminate\Http\Resources\Json\JsonResource;

class ExportacionMineralesResource extends JsonResource
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
            "kilosNetosHumedos" => round($this->kilosNetosHumedos, 2),
            "humedadPorcentaje" => round($this->humedadPorcentaje, 2),
            "humedadValor" => round($this->humedadValor, 2),
            "mermaPorcentaje" => round($this->mermaPorcentaje, 2),
            "mermaValor" => round($this->mermaValor, 2),
            "kilosNetosSecos" => round($this->kilosNetosSecos, 2),
            "gastosRealizacion" => round($this->gastosRealizacion, 2),
            "codigoMoneda" => $this->codigoMoneda,
            "montoTotalMoneda" => round($this->montoTotalMoneda, 2),
            "montoTotal" => round($this->montoTotal, 2),
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
            "otrosDatos"=>json_encode($this->otrosDatos),
            'detalles' => DetalleExportacionMineralesResource::collection(collect($this->detalles)),
            "emailCliente" => $this->emailCliente,
            "cafc" => $this->cafc,
            "codigoExcepcion" => $this->codigoExcepcion,
            "descuentoAdicional" => round($this->descuentoAdicional, 2),
            
        ];
    }
}
