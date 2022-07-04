<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\ClientFel\Models\FelCaption;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

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
        try{
            $number_literal = to_word((float)($this->montoTotal - $this->montoGiftCard), 2, 1);

        }catch (Throwable $ex) {
            $number_literal = "";
        }

        $company_id = $this->decodePrimaryKey($this->company_id);
        try{
        $branch = FelBranch::whereCompanyId($company_id)->whereCodigo($this->codigoSucursal)->first();
        $sector = \DB::table('fel_sector_document_types')->whereCodigo($this->type_document_sector_id)->first();

        $company = \DB::table('fel_company')->whereCompanyId($company_id)->select('id', 'business_name')->first();
        $caption = FelCaption::whereCompanyId($company_id)->whereCodigo($this->codigoLeyenda)->first();
        return [
            "id" => (int) $this->id,
            "ack_ticket" => $this->ack_ticket,
            "company_id" => $this->company_id,
            "id_origin" => $this->encodePrimaryKey($this->id_origin),
            "codigoMetodoPago" => (string)$this->codigoMetodoPago,
            "codigoLeyenda" => (string)$this->codigoLeyenda,
            "codigoActividad" => (string)$this->codigoActividad,
            "numeroFactura" => (string) $this->numeroFactura,
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
            "urlSin" => (string) $this->urlSin,
            "otrosDatos" => json_encode($this->otrosDatos),
            
            // compra venta v2
            "descuentoAdicional" => (string)$this->descuentoAdicional ?? null,
            "montoGiftCard" => (string)$this->montoGiftCard ?? null,
            "codigoExcepcion" => (string)$this->codigoExcepcion ?? null,
            "cafc" => (string)$this->cafc ?? null,


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
            "costosGastosNacionales" => !empty($this->costosGastosNacionales) ?  json_encode($this->costosGastosNacionales) : "",
            "costosGastosInternacionales" => !empty($this->costosGastosInternacionales) ?  json_encode($this->costosGastosInternacionales) : "",

            // factura nota debito crédito
            "numeroNota" => (int) $this->numero_factura,
            "numeroAutorizacionCuf" => (string) $this->numeroAutorizacionCuf, // cuf invoice ref
            "montoDescuentoCreditoDebito" => (string) $this->montoDescuentoCreditoDebito,
            "montoEfectivoCreditoDebito" => (string) $this->montoEfectivoCreditoDebito,

            // nota crédito débito
            "idFacturaOriginal" => (string)$this->factura_original_id_hashed,
            // factura sector educativo
            "nombreEstudiante" => $this->nombreEstudiante,
            "periodoFacturado" => $this->periodoFacturado,
            
            // hidrocarburos
            "ciudad" => $this->ciudad,
            "nombrePropietario" => $this->nombrePropietario,
            "nombreRepresentanteLegal" => $this->nombreRepresentanteLegal,
            "condicionPago" => $this->condicionPago,
            "periodoEntrega" => $this->periodoEntrega,

            //servicios básicos

            "mes" => $this->mes,
            "gestion" => (int) $this->gestion,
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

            // additional data for manquiri
            "pesoBrutoGr" => $this->pesoBrutoGr,
            "pesoBrutoKg" => $this->pesoBrutoKg,
            "pesoNetoGr" => $this->pesoNetoGr,
            "numeroContrato" => (string)$this->numeroContrato ?? "",

            // Comercialización Hidrocarburos
            "placaVehiculo" => isset($this->data_specific_by_sector['placaVehiculo']) ? $this->data_specific_by_sector['placaVehiculo'] : '',
            "tipoEnvase" => isset($this->data_specific_by_sector['tipoEnvase']) ? $this->data_specific_by_sector['tipoEnvase'] : '',
            "codigoAutorizacionSC" => isset($this->data_specific_by_sector['codigoAutorizacionSC']) ? $this->data_specific_by_sector['codigoAutorizacionSC'] : '',
            "observacion" => isset($this->data_specific_by_sector['observacion']) ? $this->data_specific_by_sector['observacion'] : '',

            // Comercializacion Gnv
            "montoVale" => isset($this->data_specific_by_sector['montoVale']) ? $this->data_specific_by_sector['montoVale'] : '',

            // added extra variable to customize template
            "extras" => $this->getExtras(),

            // typeDocument
            "typeDocument" => $this->typeDocument,
            // seguros
            "ajusteAfectacionIva" => $this->ajusteAfectacionIva,

            // zona franca
            'numeroParteRecepcion' => $this->numeroParteRecepcion,

            // nota conciliacion
            
            "numeroFacturaOriginal" => isset($this->external_invoice_data) ? $this->external_invoice_data['numeroFacturaOriginal'] : null,
            "montoTotalOriginal" => isset($this->external_invoice_data) ? $this->external_invoice_data['montoTotalOriginal'] : null,
            "codigoControl" =>  isset($this->external_invoice_data) ? $this->external_invoice_data['codigoControl'] : null,
            "debitoFiscalIva" =>    isset($this->debitoFiscalIva) ? $this->debitoFiscalIva : null,
            "creditoFiscalIva" =>   isset($this->creditoFiscalIva) ? $this->creditoFiscalIva : null,
            "fechaEmisionOriginal" =>   isset($this->external_invoice_data) ? $this->external_invoice_data['fechaEmisionOriginal'] : null,
            "montoTotalConciliado" =>   isset($this->montoTotal) ? $this->montoTotal : null,

            // HIDROCARBUROS

            "ciudad" => isset($this->data_specific_by_sector['ciudad']) ? $this->data_specific_by_sector['ciudad'] : '',
            "nombrePropietario" => isset($this->data_specific_by_sector['nombrePropietario']) ? $this->data_specific_by_sector['nombrePropietario'] : '',
            "nombreRepresentanteLegal" => isset($this->data_specific_by_sector['nombreRepresentanteLegal']) ? $this->data_specific_by_sector['nombreRepresentanteLegal'] : '',
            "condicionPago" => isset($this->data_specific_by_sector['condicionPago']) ? $this->data_specific_by_sector['condicionPago'] : '',
            "periodoEntrega" => isset($this->data_specific_by_sector['periodoEntrega']) ? $this->data_specific_by_sector['periodoEntrega'] : '',
            "montoIehd" => isset($this->data_specific_by_sector['montoIehd']) ?  (string)(round($this->data_specific_by_sector['montoIehd'], 2)) : '0.00',

            //ADDITIONAL INFORMATION FROM INVOICE

            "invoiceInfo"=> [
                "titulo"=> "FACTURA",
                "tipo_factura"=>"(".ucwords( strtolower($sector->tipoFactura) ).")",
                "razon_social_emisor"=> isset($company->business_name) ? $company->business_name : '',
                "nombre_sucursal"=> $branch->codigo == 0 ? "CASA MATRIZ":"Sucursal " . $branch->codigo,
                "numero_punto_venta"=>"Punto de venta ".$this->codigoPuntoVenta,
                "direccion_sucursal"=>$branch->zona,
                "telefono_sucursal"=> "Telefono: ".$branch->telefono,
                "municipio"=> "$branch->municipio - Bolivia",
                "monto_literal"=> "SON: ". $number_literal,
                "leyenda_especifica"=> !empty($caption)? $caption->descripcion : "",
                "leyenda_fija" => FelCaption::CAPTION_SIN,
            ]
        ];
    } catch(Throwable $ex) {
        \Log::debug("error  file " . $ex->getFile(). " Line " . $ex->getLine(). " Message : " . $ex->getMessage() );
    }
    }
}
