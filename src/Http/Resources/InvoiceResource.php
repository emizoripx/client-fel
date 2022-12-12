<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\ClientFel\Models\FelCaption;
use EmizorIpx\ClientFel\Utils\Documents;
use Exception;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
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
        try {
            $invoice_info = [];
            
            if ( isset(request()->pos_info)  &&  request()->pos_info == 'true' ) {
                try {
                    $number_literal = to_word((float)($this->montoTotal - $this->montoGiftCard), 2, 1);
                } catch (Throwable $ex) {
                    $number_literal = "";
                }

                $company_id = $this->decodePrimaryKey($this->company_id);

                    $branch = FelBranch::whereCompanyId($company_id)->whereCodigo($this->codigoSucursal)->first();
                    $sector = \DB::table('fel_sector_document_types')->whereCodigo($this->type_document_sector_id)->first();

                    $company = \DB::table('fel_company')->whereCompanyId($company_id)->select('id', 'business_name')->first();
                    $caption = FelCaption::whereCompanyId($company_id)->whereCodigo($this->codigoLeyenda)->first();


                    $invoice_info = (object)[
                        "titulo" => "FACTURA",
                        "tipo_factura" => "(" . ucwords(strtolower($sector->tipoFactura)) . ")",
                        "razon_social_emisor" => isset($company->business_name) && !is_null($company->business_name) ? $company->business_name : '',
                        "nombre_sucursal" => $branch->codigo == 0 ? "CASA MATRIZ" : "Sucursal " . $branch->codigo,
                        "numero_punto_venta" => "Punto de venta " . $this->codigoPuntoVenta,
                        "direccion_sucursal" => isset($branch->zona) && !is_null($branch->zona) ? $branch->zona : "",
                        "telefono_sucursal" => "Telefono: " . $branch->telefono,
                        "municipio" => "$branch->municipio - Bolivia",
                        "monto_literal" => "SON: " . $number_literal,
                        "leyenda_especifica" => !empty($caption) ? $caption->descripcion : "",
                        "leyenda_fija" => FelCaption::CAPTION_SIN,
                    ];
            }
            $cuf = $this->cuf;
            $codigoEstado = $this->codigoEstado;
            if (is_numeric($this->numeroFactura)) {
                if (is_null($this->cuf)) {
                    $cuf = "CUF-TEMPORAL";
                    $codigoEstado=999;
                } 
            }


            $main = [
                "id" => (int) $this->id,
                "ack_ticket" => $this->ack_ticket,
                "company_id" => $this->company_id,
                "emitted_by" => $this->encodePrimaryKey($this->emitted_by),
                "revocated_by" => $this->encodePrimaryKey($this->revocated_by),
                "id_origin" => $this->encodePrimaryKey($this->id_origin),
                "codigoMetodoPago" => (string)$this->codigoMetodoPago,
                "codigoLeyenda" => (string)$this->codigoLeyenda,
                "codigoActividad" => (string)$this->codigoActividad,
                "numeroFactura" => $this->typeDocument == 0 ? (string) $this->numeroFactura : (string) $this->document_number,
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
                "montoTotal" => $this->montoTotal,
                "montoTotalMoneda" => $this->montoTotalMoneda,
                "usuario" => $this->usuario,
                "created_at" => $this->created_at,
                "updated_at" => $this->updated_at,
                "cuf" => $cuf,
                "sin_status" => $this->estado,
                "codigoEstado" => $codigoEstado,
                "sin_errors" => ( !empty($this->errores) && isset($this->errores)) ? json_encode($this->errores) : '',
                "emission_type" => $this->emission_type,
                "codigoTipoFactura" => (int) $this->type_invoice_id,
                "codigo_sucursal" => (string)$this->codigoSucursal,
                "codigo_pos" => (string)$this->codigoPuntoVenta,
                "numeroTarjeta" => (int) $this->numeroTarjeta,
                "urlSin" => (string) $this->urlSin,
                "otrosDatos" => json_encode($this->otrosDatos),
                "descuentoAdicional" => (string)$this->descuentoAdicional ?? null,
                "codigoExcepcion" => (string)$this->codigoExcepcion ?? null,
                "cafc" => (string)$this->cafc ?? null,
                "extras" => $this->getExtras(),
                "typeDocument" => $this->typeDocument,
                "sector_document_type_id" => $this->type_document_sector_id ?? null,
                "invoiceInfo" => $invoice_info
            ];


            switch ($this->type_document_sector_id) {
                case TypeDocumentSector::COMPRA_VENTA:

                    $array_data = [];
                    if( $this->typeDocument == Documents::NOTA_RECEPCION ) {
                        $delivered_origin = $this->invoice_origin();
                        $array_data = [
                            "idFacturaOriginal" => (string)$this->factura_original_id_hashed,
                            "numeroFacturaOriginal" => isset($delivered_origin) ? (string) $this->document_number : null,
                            "fechaEmisionOriginal" => isset($delivered_origin) ? (string) $this->created_at : null,
                        ];
                    }

                    return array_merge($main, $array_data , [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                    ]);
                    
                case TypeDocumentSector::PREVALORADA:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                    ]);
                    
                case TypeDocumentSector::ALQUILER_BIENES_INMUEBLES:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "periodoFacturado" => $this->periodoFacturado ?? '',
                    ]);
                    
                // case TypeDocumentSector::COMERCIAL_EXPORTACION_LIBRE_CONSIGNACION:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //     ]);
                    
                case TypeDocumentSector::ZONA_FRANCA:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        'numeroParteRecepcion' => $this->numeroParteRecepcion,
                    ]);
                    
                // case TypeDocumentSector::TASA_CERO:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //     ]);
                // case TypeDocumentSector::EXPORTACION_MINERALES:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //         "direccionComprador" => $this->direccionComprador,
                //         "concentradoGranel" => $this->concentradoGranel,
                //         "origen" => $this->origen,
                //         "puertoTransito" => $this->puertoTransito,
                //         "incoterm" => $this->incoterm,
                //         "puertoDestino" => $this->puertoDestino,
                //         "paisDestino" => (string)$this->paisDestino,
                //         "tipoCambioANB" => round((float)$this->tipoCambioANB, 2),
                //         "numeroLote" => $this->numeroLote,
                //         "kilosNetosHumedos" => $this->kilosNetosHumedos,
                //         "humedadValor" => $this->humedadValor,
                //         "humedadPorcentaje" => $this->humedadPorcentaje,
                //         "mermaValor" => $this->mermaValor,
                //         "mermaPorcentaje" => $this->mermaPorcentaje,
                //         "kilosNetosSecos" => $this->kilosNetosSecos,
                //         "gastosRealizacion" => $this->gastosRealizacion,
                //         "monedaTransaccional" => $this->otrosDatos->monedaTransaccional ?? null,
                //         "fleteInternoUSD" => $this->otrosDatos->fleteInternoUSD ?? null,
                //         "valorFobFrontera" => $this->otrosDatos->valorFobFrontera ?? null,
                //         "valorPlata" => $this->otrosDatos->valorPlata ?? null,
                //         "valorFobFronteraBs" => $this->otrosDatos->valorFobFronteraBs ?? null,
                //         // // additional data for manquiri
                //         "pesoBrutoGr" => $this->pesoBrutoGr,
                //         "pesoBrutoKg" => $this->pesoBrutoKg,
                //         "pesoNetoGr" => $this->pesoNetoGr,
                //         "numeroContrato" => (string)$this->numeroContrato ?? "",
                //     ]);
                case TypeDocumentSector::SECTORES_EDUCATIVOS:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "nombreEstudiante" => $this->nombreEstudiante,
                        "periodoFacturado" => $this->periodoFacturado,
                    ]);
                // case TypeDocumentSector::COMERCIALIZACION_HIDROCARBUROS:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //         "placaVehiculo" => isset($this->data_specific_by_sector['placaVehiculo']) ? $this->data_specific_by_sector['placaVehiculo'] : '',
                //         "tipoEnvase" => isset($this->data_specific_by_sector['tipoEnvase']) ? $this->data_specific_by_sector['tipoEnvase'] : '',
                //         "codigoAutorizacionSC" => isset($this->data_specific_by_sector['codigoAutorizacionSC']) ? $this->data_specific_by_sector['codigoAutorizacionSC'] : '',
                //         "observacion" => isset($this->data_specific_by_sector['observacion']) ? $this->data_specific_by_sector['observacion'] : '',


                //     ]);
                // case TypeDocumentSector::COMERCIALIZACION_GNV:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //         "placaVehiculo" => isset($this->data_specific_by_sector['placaVehiculo']) ? $this->data_specific_by_sector['placaVehiculo'] : '',
                //         "tipoEnvase" => isset($this->data_specific_by_sector['tipoEnvase']) ? $this->data_specific_by_sector['tipoEnvase'] : '',
                //         "montoVale" => isset($this->data_specific_by_sector['montoVale']) ? $this->data_specific_by_sector['montoVale'] : '',
                //     ]);
                // case TypeDocumentSector::SERVICIOS_BASICOS:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //         "mes" => $this->mes,
                //         "gestion" => (int) $this->gestion,
                //         "ciudad" => $this->ciudad,
                //         "zona" => $this->zona,
                //         "numeroMedidor" => $this->numeroMedidor,
                //         "domicilioCliente" => $this->domicilioCliente,
                //         "consumoPeriodo" => $this->consumoPeriodo,
                //         "beneficiarioLey1886" => $this->beneficiarioLey1886,
                //         "montoDescuentoLey1886" => $this->montoDescuentoLey1886,
                //         "montoDescuentoTarifaDignidad" => $this->montoDescuentoTarifaDignidad,
                //         "tasaAseo" => $this->tasaAseo,
                //         "tasaAlumbrado" => $this->tasaAlumbrado,
                //         "ajusteNoSujetoIva" => $this->ajusteNoSujetoIva,
                //         "detalleAjusteNoSujetoIva" => $this->detalleAjusteNoSujetoIva,
                //         "ajusteSujetoIva" => $this->ajusteSujetoIva,
                //         "detalleAjusteSujetoIva" => $this->detalleAjusteSujetoIva,
                //         "otrosPagosNoSujetoIva" => $this->otrosPagosNoSujetoIva,
                //         "detalleOtrosPagosNoSujetoIva" => $this->detalleOtrosPagosNoSujetoIva,
                //     ]);
                // case TypeDocumentSector::HIDROCARBUROS_IEHD:
                //     return array_merge($main, [
                //         "ciudad" => isset($this->data_specific_by_sector['ciudad']) ? $this->data_specific_by_sector['ciudad'] : '',
                //         "nombrePropietario" => isset($this->data_specific_by_sector['nombrePropietario']) ? $this->data_specific_by_sector['nombrePropietario'] : '',
                //         "nombreRepresentanteLegal" => isset($this->data_specific_by_sector['nombreRepresentanteLegal']) ? $this->data_specific_by_sector['nombreRepresentanteLegal'] : '',
                //         "condicionPago" => isset($this->data_specific_by_sector['condicionPago']) ? $this->data_specific_by_sector['condicionPago'] : '',
                //         "periodoEntrega" => isset($this->data_specific_by_sector['periodoEntrega']) ? $this->data_specific_by_sector['periodoEntrega'] : '',
                //         "montoIehd" => isset($this->data_specific_by_sector['montoIehd']) ?  (string)(round($this->data_specific_by_sector['montoIehd'], 2)) : '0.00',
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //     ]);
                // case TypeDocumentSector::HOTELES:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //     ]);
                // case TypeDocumentSector::HOSPITALES_CLINICAS:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //     ]);
                // case TypeDocumentSector::VENTA_INTERNA_MINERALES:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //         "direccionComprador" => $this->direccionComprador,
                //         "concentradoGranel" => $this->concentradoGranel,
                //         "origen" => $this->origen,
                //         "puertoTransito" => $this->puertoTransito,
                //         "incoterm" => $this->incoterm,
                //         "puertoDestino" => $this->puertoDestino,
                //         "paisDestino" => (string)$this->paisDestino,
                //         "tipoCambioANB" => round((float)$this->tipoCambioANB, 2),
                //         "numeroLote" => $this->numeroLote,
                //         "kilosNetosHumedos" => $this->kilosNetosHumedos,
                //         "humedadValor" => $this->humedadValor,
                //         "humedadPorcentaje" => $this->humedadPorcentaje,
                //         "mermaValor" => $this->mermaValor,
                //         "mermaPorcentaje" => $this->mermaPorcentaje,
                //         "kilosNetosSecos" => $this->kilosNetosSecos,
                //         "gastosRealizacion" => $this->gastosRealizacion,
                //         "monedaTransaccional" => $this->otrosDatos->monedaTransaccional ?? null,
                //         "fleteInternoUSD" => $this->otrosDatos->fleteInternoUSD ?? null,
                //         "valorFobFrontera" => $this->otrosDatos->valorFobFrontera ?? null,
                //         "valorPlata" => $this->otrosDatos->valorPlata ?? null,
                //         "valorFobFronteraBs" => $this->otrosDatos->valorFobFronteraBs ?? null,
                //         "liquidacionPreliminar" => (string) $this->liquidacion_preliminar,
                //         "iva" => (string) $this->iva,
                //     ]);
                // case TypeDocumentSector::COMERCIAL_EXPORTACION:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //         "lugarDestino" => $this->lugarDestino,
                //         "incotermDetalle" => $this->incoterm_detalle,
                //         "gastoTransporteNacional" => !empty($this->costosGastosNacionales['gastoTransporte']) ? (string) $this->costosGastosNacionales['gastoTransporte'] : "",
                //         "gastoSeguroNacional" => !empty($this->costosGastosNacionales['gastoSeguro']) ? (string) $this->costosGastosNacionales['gastoSeguro'] : "",
                //         "gastoTransporteInternacional" => !empty($this->costosGastosInternacionales['gastoTransporte']) ? (string) $this->costosGastosInternacionales['gastoTransporte'] : "",
                //         "gastoSeguroInternacional" => !empty($this->costosGastosInternacionales['gastoSeguro']) ? (string) $this->costosGastosInternacionales['gastoSeguro'] : "",
                //         "totalGastosNacionalesFob" => $this->totalGastosNacionalesFob,
                //         "totalGastosInternacionales" => $this->totalGastosInternacionales,
                //         "numeroDescripcionPaquetesBultos" => $this->numeroDescripcionPaquetesBultos,
                //         "informacionAdicional" => $this->informacionAdicional,
                //         "costosGastosNacionales" => !empty($this->costosGastosNacionales) ?  json_encode($this->costosGastosNacionales) : "",
                //         "costosGastosInternacionales" => !empty($this->costosGastosInternacionales) ?  json_encode($this->costosGastosInternacionales) : "",
                //     ]);
                // case TypeDocumentSector::TELECOMUNICACIONES:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //     ]);
                // case TypeDocumentSector::PREVALORADA:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //     ]);
                // case TypeDocumentSector::DEBITO_CREDITO:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //         "idFacturaOriginal" => (string)$this->factura_original_id_hashed,
                //         "numeroNota" => (int) $this->numero_factura,
                //         "numeroAutorizacionCuf" => (string) $this->numeroAutorizacionCuf, // cuf invoice ref
                //         "montoDescuentoCreditoDebito" => (string) $this->montoDescuentoCreditoDebito,
                //         "montoEfectivoCreditoDebito" => (string) $this->montoEfectivoCreditoDebito,
            
                //     ]);
                // case TypeDocumentSector::COMERCIAL_EXPORTACION_SERVICIOS:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //     ]);
                // case TypeDocumentSector::NOTA_CONCILIACION:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //         "numeroFacturaOriginal" => isset($this->external_invoice_data) ? $this->external_invoice_data['numeroFacturaOriginal'] : null,
                //         "montoTotalOriginal" => isset($this->external_invoice_data) ? $this->external_invoice_data['montoTotalOriginal'] : null,
                //         "codigoControl" =>  isset($this->external_invoice_data) ? $this->external_invoice_data['codigoControl'] : null,
                //         "debitoFiscalIva" =>    isset($this->debitoFiscalIva) ? $this->debitoFiscalIva : null,
                //         "creditoFiscalIva" =>   isset($this->creditoFiscalIva) ? $this->creditoFiscalIva : null,
                //         "fechaEmisionOriginal" =>   isset($this->external_invoice_data) ? $this->external_invoice_data['fechaEmisionOriginal'] : null,
                //         "montoTotalConciliado" =>   isset($this->montoTotal) ? $this->montoTotal : null,


                    
                //     ]);
                // case TypeDocumentSector::SEGUROS:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //         "ajusteAfectacionIva" => $this->ajusteAfectacionIva,
                //     ]);
                case TypeDocumentSector::COMPRA_VENTA_BONIFICACIONES:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                    ]);
                // case TypeDocumentSector::HIDROCARBUROS_NO_IEHD:
                //     return array_merge($main, [
                //         "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                //         "tipoCambio" => round((float)$this->tipoCambio, 2),
                //         "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                //     ]);

                default:
                    return [
                        "id" => (int) $this->id,
                        "ack_ticket" => $this->ack_ticket,
                        "company_id" => $this->company_id,
                        "emitted_by" => $this->encodePrimaryKey($this->emitted_by),
                        "revocated_by" => $this->encodePrimaryKey($this->revocated_by),
                        "id_origin" => $this->encodePrimaryKey($this->id_origin),
                        "codigoMetodoPago" => (string)$this->codigoMetodoPago,
                        "codigoLeyenda" => (string)$this->codigoLeyenda,
                        "codigoActividad" => (string)$this->codigoActividad,
                        "numeroFactura" => $this->typeDocument == 0 ? (string) $this->numeroFactura : (string) $this->document_number,
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
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoTotal" => $this->montoTotal,
                        "montoTotalMoneda" => $this->montoTotalMoneda,
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "usuario" => $this->usuario,
                        "created_at" => $this->created_at,
                        "updated_at" => $this->updated_at,
                        "cuf" => $this->cuf,
                        "sin_status" => $this->estado,
                        "codigoEstado" => $this->codigoEstado,
                        "sin_errors" => (!empty($this->errores) && isset($this->errores)) ? json_encode($this->errores) : '',
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
                        "codigoControl" =>  isset($this->external_invoice_data) && isset($this->external_invoice_data['codigoControl']) ? $this->external_invoice_data['codigoControl'] : null,
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

                        "invoiceInfo" => $invoice_info
                    ];
                    break;
            }
        }    catch(Throwable $ex) {
            \Log::debug("error  file " . $ex->getFile(). " Line " . $ex->getLine(). " Message : " . $ex->getMessage() );
        }
    }
}
