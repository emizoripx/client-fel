<?php

namespace EmizorIpx\ClientFel\Services\OfflineEvent\Resources\CompraVenta;

use Illuminate\Http\Resources\Json\JsonResource;

class CompraVentaResource extends JsonResource
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
            "cuf" => $this->cuf,
            "cuis" => $this->cuis,
            "cufd" => $this->cufd,
            "telefonoCliente" => $this->telefonoCliente,
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
            'detalles' => DetalleCompraVentaResource::collection(collect($this->detalles)),
            "emailCliente" => $this->emailCliente,
            "cafc" => $this->cafc,
            "codigoExcepcion" => $this->codigoExcepcion,
            "montoGiftCard" => round($this->montoGiftCard,2),
            "descuentoAdicional" => round($this->descuentoAdicional,2),
            "extras" => json_decode($this->extras)
        ];
    }
}
