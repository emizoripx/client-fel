<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\ComercializacionHidrocarburos;

use Illuminate\Http\Resources\Json\JsonResource;

class ComercializacionHidrocarburosResource extends JsonResource
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
            "placaVehiculo" => isset($this->data_specific_by_sector['placaVehiculo']) ? $this->data_specific_by_sector['placaVehiculo'] : '',
            "tipoEnvase" => isset($this->data_specific_by_sector['tipoEnvase']) ? $this->data_specific_by_sector['tipoEnvase'] : '',
            "codigoAutorizacionSC" => isset($this->data_specific_by_sector['codigoAutorizacionSC']) ? $this->data_specific_by_sector['codigoAutorizacionSC'] : '', 
            "observacion" => isset($this->data_specific_by_sector['observacion']) ? $this->data_specific_by_sector['observacion'] : '', 
            "codigoPais" => $this->paisDestino,
            'detalles' => DetalleComercializacionHidrocarburos::collection(collect($this->detalles)),
            "emailCliente" => $this->emailCliente,
            "cafc" => $this->cafc,
            "codigoExcepcion" => $this->codigoExcepcion,
            "descuentoAdicional" => round($this->descuentoAdicional,2),
            "extras" => json_decode($this->extras)
        ];
    }
}
