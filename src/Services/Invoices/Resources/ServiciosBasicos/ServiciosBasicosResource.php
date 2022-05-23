<?php

namespace EmizorIpx\ClientFel\Services\Invoices\Resources\ServiciosBasicos;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiciosBasicosResource extends JsonResource
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
            "otrasTasas" => round($this->otrasTasas, 2),
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
            'detalles' => DetalleServiciosBasicosResource::collection(collect($this->detalles)),
            "emailCliente" => $this->emailCliente,
            "cafc" => $this->cafc,
            "codigoExcepcion" => $this->codigoExcepcion,
            "descuentoAdicional" => round($this->descuentoAdicional,2),
            "mes" => $this->mes,
            "gestion" => $this->gestion,
            "ciudad" => $this->ciudad,
            "zona" => $this->zona,
            "numeroMedidor" => $this->numeroMedidor,
            "domicilioCliente" => $this->domicilioCliente,
            "consumoPeriodo" => $this->consumoPeriodo,
            "beneficiarioLey1886" => $this->beneficiarioLey1886,
            "montoDescuentoLey1886" => $this->montoDescuentoLey1886,
            "montoDescuentoTarifaDignidad" => $this->montoDescuentoTarifaDignidad,
            "tasaAseo" => $this->tasaAseo,
            "tasaAlumbrado" => $this->tasaAlumbrado,
            "ajusteNoSujetoIva" => $this->ajusteNoSujetoIva,
            "detalleAjusteNoSujetoIva" => $this->detalleAjusteNoSujetoIva,
            "ajusteSujetoIva" => $this->ajusteSujetoIva,
            "detalleAjusteSujetoIva" => $this->detalleAjusteSujetoIva,
            "otrosPagosNoSujetoIva" => $this->otrosPagosNoSujetoIva,
            "detalleOtrosPagosNoSujetoIva" => $this->detalleOtrosPagosNoSujetoIva,
        ];
    }
}
