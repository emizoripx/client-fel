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
            "codigoMetodoPago" =>(int) $this->codigoMetodoPago,
            "codigoLeyenda" => (int) $this->codigoLeyenda,
            "codigoActividad" => (int) $this->codigoActividad,
            "numeroFactura" => (int) $this->numeroFactura,
            "fechaEmision" => $this->fechaEmision,
            "nombreRazonSocial" => $this->nombreRazonSocial,
            "codigoTipoDocumentoIdentidad" => (int) $this->codigoTipoDocumentoIdentidad,
            "numeroDocumento" => $this->numeroDocumento,
            "complemento" => $this->complemento,
            "codigoCliente" => $this->codigoCliente,
            "emailCliente" => $this->emailCliente,
            "telefonoCliente" => $this->telefonoCliente,
            "codigoPuntoVenta" => (int) $this->codigoPuntoVenta,
            "codigoMoneda" => (int) $this->codigoMoneda,
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
            "paisDestino" => (int) $this->paisDestino,
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
            "codigo_sucursal" => (int) $this->codigoSucursal,
            "codigo_pos" => (int) $this->codigoPuntoVenta,
            "numeroTarjeta" => (int) $this->numeroTarjeta,

            // factura venta minerales
            "liquidacionPreliminar" => (string) $this->liquidacion_preliminar,
            "iva" => (string) $this->iva,
            
            // factura comercial exportacion
            "lugarDestino" => $this->lugarDestino,
            "incoterm_detalle" => $this->incoterm_detalle,
            "gastoTransporteNacional" => $this->totalGastosNacionalesFob['gastoTransporte'],
            "gastoSeguroNacional" => $this->totalGastosNacionalesFob['gastoSeguro'],
            "gastoTransporteInternacional" => $this->totalGastosInternacionales['gastoTransporte'],
            "gastoSeguroInternacional" => $this->totalGastosInternacionales['gastoSeguro'],
            "totalGastosNacionalesFob" => $this->totalGastosNacionalesFob,
            "totalGastosInternacionales" => $this->totalGastosInternacionales,
            "numeroDescripcionPaquetesBultos" => $this->numeroDescripcionPaquetesBultos,
            "informacionAdicional" => $this->informacionAdicional,

        ];
    }
}
