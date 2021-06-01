<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    use MakesHash;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        return [
            "id" => (int) $this->id,
            "company_id" => $this->company_id,
            "id_origin" => $this->encodePrimaryKey($this->id_origin),
            "codigoMetodoPago" => (string)$this->codigoMetodoPago,
            "codigoLeyenda" => (string)$this->codigoLeyenda,
            "codigoActividad" => (string)$this->codigoActividad,
            "numeroFactura" => (int) $this->numeroFactura,
            "fechaEmision" => $this->fechaEmision,
            "nombreRazonSocial" => $this->nombreRazonSocial,
            "codigoTipoDocumentoIdentidad" => (string)$this->codigoTipoDocumentoIdentidad,
            "numeroDocumento" => $this->numeroDocumento,
            "complemento" => $this->complemento,
            "codigoCliente" => $this->codigoCliente,
            "emailCliente" => $this->emailCliente,
            "telefonoCliente" => $this->telefonoCliente,
            "codigoPuntoVenta" => (string)$this->codigoPuntoVenta,
            "codigoMoneda" => (string)$this->codigoMoneda,
            "tipoCambio" => round((float)$this->tipoCambio,2),
            "montoTotal" => $this->montoTotal,
            "montoTotalMoneda" => $this->montoTotalMoneda,
            "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
            "usuario" => $this->usuario,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "cuf" => $this->cuf,
            "sin_status" => $this->estado,
            "codigoEstado" => $this->codigoEstado,
            "sin_errors" => $this->errores,
            "direccionComprador" => $this->direccionComprador,
            "concentradoGranel" => $this->concentradoGranel,
            "origen" => $this->origen,
            "puertoTransito" => $this->puertoTransito,
            "incoterm" => $this->incoterm,
            "puertoDestino" => $this->puertoDestino,
            "paisDestino" => (string)$this->paisDestino,
            "tipoCambioANB" => round((float)$this->tipoCambioANB, 2),
            "numeroLote" => $this->numeroLote,
            "kilosNetosHumedos" => $this->kilosNetosHumedos,
            "humedadValor" => $this->humedadValor,
            "humedadPorcentaje" => $this->humedadPorcentaje,
            "mermaValor" => $this->mermaValor,
            "mermaPorcentaje" => $this->mermaPorcentaje,
            "kilosNetosSecos" => $this->kilosNetosSecos,
            "gastosRealizacion" => $this->gastosRealizacion,
            "monedaTransaccional" => $this->otrosDatos->monedaTransaccional ?? null,
            "fleteInternoUSD" => $this->otrosDatos->fleteInternoUSD ?? null,
            "valorFobFrontera" => $this->otrosDatos->valorFobFrontera ?? null,
            "valorPlata" => $this->otrosDatos->valorPlata ?? null,
            "valorFobFronteraBs" => $this->otrosDatos->valorFobFronteraBs ?? null,
            "sector_document_type_id" => $this->type_document_sector_id ?? null,
            "emission_type" => $this->emission_type,
            "codigoTipoFactura" => (int) $this->type_invoice_id,
            "codigo_sucursal" => (string)$this->codigoSucursal,
            "codigo_pos" => (string)$this->codigoPuntoVenta,
            "numeroTarjeta" => (int) $this->numeroTarjeta,

            // factura venta minerales
            "liquidacionPreliminar" => (string) $this->liquidacion_preliminar,
            "iva" => (string) $this->iva,
            
            // factura comercial exportacion
            "lugarDestino" => $this->lugarDestino,
            "incotermDetalle" => $this->incoterm_detalle,
            "gastoTransporteNacional" => !empty($this->costosGastosNacionales['gastoTransporte']) ? (string) $this->costosGastosNacionales['gastoTransporte'] : "",
            "gastoSeguroNacional" => !empty($this->costosGastosNacionales['gastoSeguro']) ? (string) $this->costosGastosNacionales['gastoSeguro'] : "",
            "gastoTransporteInternacional" => !empty($this->costosGastosInternacionales['gastoTransporte']) ? (string) $this->costosGastosInternacionales['gastoTransporte'] : "",
            "gastoSeguroInternacional" => !empty($this->costosGastosInternacionales['gastoSeguro']) ? (string) $this->costosGastosInternacionales['gastoSeguro'] : "",
            "totalGastosNacionalesFob" => $this->totalGastosNacionalesFob,
            "totalGastosInternacionales" => $this->totalGastosInternacionales,
            "numeroDescripcionPaquetesBultos" => $this->numeroDescripcionPaquetesBultos,
            "informacionAdicional" => $this->informacionAdicional,

            // factura nota debito crédito
            "numeroNota" => (int) $this->numero_factura,
            "numeroAutorizacionCuf" => (string) $this->numeroAutorizacionCuf, // cuf invoice ref
            "montoDescuentoCreditoDebito" => (string) $this->montoDescuentoCreditoDebito,
            "montoEfectivoCreditoDebito" => (string) $this->montoEfectivoCreditoDebito,

        ];
    }
}
