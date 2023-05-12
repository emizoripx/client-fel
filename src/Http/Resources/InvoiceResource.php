<?php

namespace EmizorIpx\ClientFel\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\ClientFel\Models\FelCaption;
use EmizorIpx\ClientFel\Utils\Documents;
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
                "id" => (int) rand(100, 9999) ,
                "ack_ticket" => "abcdf",
                
                "company_id" => $this->encodePrimaryKey($this->company_id),
                "emitted_by" => $this->encodePrimaryKey($this->emitted_by),
                "revocated_by" => $this->encodePrimaryKey($this->revocated_by),
                "id_origin" => $this->encodePrimaryKey($this->id),
                "codigoMetodoPago" => (string)$this->codigoMetodoPago,
                "codigoLeyenda" => (string)$this->codigoLeyenda,
                "codigoActividad" => (string)$this->codigoActividad,
                "numeroFactura" => $this->typeDocument == 0 ? (string) $this->numeroFactura : (string) $this->document_number,
                "fechaEmision" => $this->fechaEmision,
                "nombreRazonSocial" => $this->nombreRazonSocial,
                "codigoTipoDocumentoIdentidad" => (string)$this->codigoTipoDocumentoIdentidad,
                "numeroDocumento" => $this->numeroDocumento,
                "complemento" => $this->complemento,
                "codigoCliente" => (string)$this->codigoCliente,
                "emailCliente" => $this->emailCliente,
                "telefonoCliente" => $this->telefonoCliente,
                "codigoPuntoVenta" => (string)$this->codigoPuntoVenta,
                "codigoMoneda" => (string)$this->codigoMoneda,
                "montoTotal" => $this->montoTotal,
                "montoTotalMoneda" => $this->montoTotalMoneda,
                "usuario" => $this->usuario,
                "cuf" => $cuf,
                "sin_status" => $this->estado,
                "codigoEstado" => $codigoEstado,
                "sin_errors" => ( !empty($this->errores) && isset($this->errores)) ? json_encode($this->errores) : '',
                "emission_type" => "En Linea",// $this->emission_type,
                "codigoTipoFactura" => (int) $this->type_invoice_id,
                "codigo_sucursal" => (string)$this->codigoSucursal,
                "codigo_pos" => (string)$this->codigoPuntoVenta,
                "numeroTarjeta" => (int) $this->numeroTarjeta,
                "urlSin" => (string) $this->urlSin,
                "descuentoAdicional" => (string)$this->descuentoAdicional ?? null,
                "codigoExcepcion" => (string)$this->codigoExcepcion ?? null,
                "cafc" => (string)$this->cafc ?? null,
                "extras" => null,//$this->getExtras(),
                "typeDocument" => $this->typeDocument,
                "sector_document_type_id" => $this->type_document_sector_id ?? null,
                "invoiceInfo" => $invoice_info,
                "otrosDatos" => isset($this->data_specific_by_sector['otrosDatos']) ? json_encode($this->data_specific_by_sector['otrosDatos']) : null,
            ];


            switch ($this->type_document_sector_id) {
                case TypeDocumentSector::COMPRA_VENTA:

                    $array_data = [];
                    if( $this->typeDocument == Documents::NOTA_RECEPCION ) {
                        $delivered_origin = $this;
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
                        "periodoFacturado" => isset($this->data_specific_by_sector['periodoFacturado']) ? $this->data_specific_by_sector['periodoFacturado'] : '',
                    ]);
                    
                case TypeDocumentSector::COMERCIAL_EXPORTACION_LIBRE_CONSIGNACION:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                    ]);
                    
                case TypeDocumentSector::ZONA_FRANCA:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        'numeroParteRecepcion' => isset($this->data_specific_by_sector['numeroParteRecepcion']) ? $this->data_specific_by_sector['numeroParteRecepcion'] : '',
                    ]);
                    
                case TypeDocumentSector::TASA_CERO:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                    ]);
                case TypeDocumentSector::EXPORTACION_MINERALES:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "lugarDestino" => $this->lugarDestino,
                        "incotermDetalle" => $this->incoterm_detalle,
                        "direccionComprador" => $this->data_specific_by_sector['direccionComprador'],
                        "concentradoGranel" => $this->data_specific_by_sector['concentradoGranel'],
                        "origen" => $this->data_specific_by_sector['origen'],
                        "puertoTransito" => $this->data_specific_by_sector['puertoTransito'],
                        "incoterm" => $this->data_specific_by_sector['incoterm'],
                        "puertoDestino" => $this->data_specific_by_sector['puertoDestino'],
                        "paisDestino" => (string)$this->data_specific_by_sector['paisDestino'],
                        "tipoCambioANB" => round((float)$this->data_specific_by_sector['tipoCambioANB'], 2),
                        "numeroLote" => $this->data_specific_by_sector['numeroLote'],
                        "kilosNetosHumedos" => $this->data_specific_by_sector['kilosNetosHumedos'],
                        "humedadValor" => $this->data_specific_by_sector['humedadValor'],
                        "humedadPorcentaje" => $this->data_specific_by_sector['humedadPorcentaje'],
                        "mermaValor" => $this->data_specific_by_sector['mermaValor'],
                        "mermaPorcentaje" => $this->data_specific_by_sector['mermaPorcentaje'],
                        "kilosNetosSecos" => $this->data_specific_by_sector['kilosNetosSecos'],
                        "gastosRealizacion" => $this->data_specific_by_sector['gastosRealizacion'],
                        "monedaTransaccional" => $this->data_specific_by_sector['otrosDatos']->monedaTransaccional ?? null,
                        "fleteInternoUSD" => $this->data_specific_by_sector['otrosDatos']->fleteInternoUSD ?? null,
                        "valorFobFrontera" => $this->data_specific_by_sector['otrosDatos']->valorFobFrontera ?? null,
                        "valorPlata" => $this->data_specific_by_sector['otrosDatos']->valorPlata ?? null,
                        "valorFobFronteraBs" => $this->data_specific_by_sector['otrosDatos']->valorFobFronteraBs ?? null,
                        // // additional data for manquiri
                        "pesoBrutoGr" => $this->data_specific_by_sector['pesoBrutoGr'],
                        "pesoBrutoKg" => $this->data_specific_by_sector['pesoBrutoKg'],
                        "pesoNetoGr" => $this->data_specific_by_sector['pesoNetoGr'],
                        "numeroContrato" => (string)$this->data_specific_by_sector['numeroContrato'] ?? "",
                    ]);
                case TypeDocumentSector::SECTORES_EDUCATIVOS:
                    info("ingresando a sectores educativos");
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "nombreEstudiante" => isset($this->data_specific_by_sector['nombreEstudiante']) ? $this->data_specific_by_sector['nombreEstudiante'] : '',
                        "periodoFacturado" => isset($this->data_specific_by_sector['periodoFacturado']) ? $this->data_specific_by_sector['periodoFacturado'] : '',
                    ]);
                case TypeDocumentSector::COMERCIALIZACION_HIDROCARBUROS:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "placaVehiculo" => isset($this->data_specific_by_sector['placaVehiculo']) ? $this->data_specific_by_sector['placaVehiculo'] : '',
                        "tipoEnvase" => isset($this->data_specific_by_sector['tipoEnvase']) ? $this->data_specific_by_sector['tipoEnvase'] : '',
                        "codigoAutorizacionSC" => isset($this->data_specific_by_sector['codigoAutorizacionSC']) ? $this->data_specific_by_sector['codigoAutorizacionSC'] : '',
                        "observacion" => isset($this->data_specific_by_sector['observacion']) ? $this->data_specific_by_sector['observacion'] : '',


                    ]);
                case TypeDocumentSector::ENTIDADES_FINANCIERAS:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoTotalArrendamientoFinanciero" => isset($this->data_specific_by_sector['montoTotalArrendamientoFinanciero']) ? $this->data_specific_by_sector['montoTotalArrendamientoFinanciero'] : '',
                    ]);
                case TypeDocumentSector::COMERCIALIZACION_GNV:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "placaVehiculo" => isset($this->data_specific_by_sector['placaVehiculo']) ? $this->data_specific_by_sector['placaVehiculo'] : '',
                        "tipoEnvase" => isset($this->data_specific_by_sector['tipoEnvase']) ? $this->data_specific_by_sector['tipoEnvase'] : '',
                        "montoVale" => isset($this->data_specific_by_sector['montoVale']) ? $this->data_specific_by_sector['montoVale'] : '',
                    ]);
                case TypeDocumentSector::SERVICIOS_BASICOS:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,

                        "mes" => $this->data_specific_by_sector['mes'],
                        "gestion" => (int) $this->data_specific_by_sector['gestion'],
                        "ciudad" => $this->data_specific_by_sector['ciudad'],
                        "zona" => $this->data_specific_by_sector['zona'],
                        "numeroMedidor" => $this->data_specific_by_sector['numeroMedidor'],
                        "domicilioCliente" => $this->data_specific_by_sector['domicilioCliente'],
                        "consumoPeriodo" => $this->data_specific_by_sector['consumoPeriodo'],
                        "beneficiarioLey1886" => $this->data_specific_by_sector['beneficiarioLey1886'],
                        "montoDescuentoLey1886" => $this->data_specific_by_sector['montoDescuentoLey1886'],
                        "montoDescuentoTarifaDignidad" => $this->data_specific_by_sector['montoDescuentoTarifaDignidad'],
                        "tasaAseo" => $this->data_specific_by_sector['tasaAseo'],
                        "tasaAlumbrado" => $this->data_specific_by_sector['tasaAlumbrado'],
                        "ajusteNoSujetoIva" => $this->data_specific_by_sector['ajusteNoSujetoIva'],
                        "detalleAjusteNoSujetoIva" => $this->data_specific_by_sector['detalleAjusteNoSujetoIva'],
                        "ajusteSujetoIva" => $this->data_specific_by_sector['ajusteSujetoIva'],
                        "detalleAjusteSujetoIva" => $this->data_specific_by_sector['detalleAjusteSujetoIva'],
                        "otrosPagosNoSujetoIva" => $this->data_specific_by_sector['otrosPagosNoSujetoIva'],
                        "detalleOtrosPagosNoSujetoIva" => $this->data_specific_by_sector['detalleOtrosPagosNoSujetoIva'],
                    ]);
                case TypeDocumentSector::HIDROCARBUROS_IEHD:
                    return array_merge($main, [
                        "ciudad" => isset($this->data_specific_by_sector['ciudad']) ? $this->data_specific_by_sector['ciudad'] : '',
                        "nombrePropietario" => isset($this->data_specific_by_sector['nombrePropietario']) ? $this->data_specific_by_sector['nombrePropietario'] : '',
                        "nombreRepresentanteLegal" => isset($this->data_specific_by_sector['nombreRepresentanteLegal']) ? $this->data_specific_by_sector['nombreRepresentanteLegal'] : '',
                        "condicionPago" => isset($this->data_specific_by_sector['condicionPago']) ? $this->data_specific_by_sector['condicionPago'] : '',
                        "periodoEntrega" => isset($this->data_specific_by_sector['periodoEntrega']) ? $this->data_specific_by_sector['periodoEntrega'] : '',
                        "montoIehd" => isset($this->data_specific_by_sector['montoIehd']) ?  (string)(round($this->data_specific_by_sector['montoIehd'], 2)) : '0.00',
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                    ]);
                case TypeDocumentSector::HOTELES:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "cantidadHuespedes" => isset($this->data_specific_by_sector['cantidadHuespedes']) ? (int) $this->data_specific_by_sector['cantidadHuespedes'] : 0,
                        "cantidadHabitaciones" => isset($this->data_specific_by_sector['cantidadHabitaciones']) ? (int) $this->data_specific_by_sector['cantidadHabitaciones'] : 0,
                        "cantidadMenores" => isset($this->data_specific_by_sector['cantidadMenores']) ? (int) $this->data_specific_by_sector['cantidadMenores'] : 0,
                        "cantidadMayores" => isset($this->data_specific_by_sector['cantidadMayores']) ? (int) $this->data_specific_by_sector['cantidadMayores'] : 0,
                        "fechaIngresoHospedaje" => isset($this->data_specific_by_sector['fechaIngresoHospedaje']) ? $this->data_specific_by_sector['fechaIngresoHospedaje'] : null,

                    ]);
                case TypeDocumentSector::HOSPITALES_CLINICAS:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                    ]);
                case TypeDocumentSector::VENTA_INTERNA_MINERALES:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "direccionComprador" => $this->data_specific_by_sector['direccionComprador'],
                        "concentradoGranel" => $this->data_specific_by_sector['concentradoGranel'],
                        "origen" => $this->data_specific_by_sector['origen'],
                        "puertoTransito" => $this->data_specific_by_sector['puertoTransito'],
                        "incoterm" => $this->data_specific_by_sector['incoterm'],
                        "puertoDestino" => $this->data_specific_by_sector['puertoDestino'],
                        "paisDestino" => (string)$this->data_specific_by_sector['paisDestino'],
                        "tipoCambioANB" => round((float)$this->data_specific_by_sector['tipoCambioANB'], 2),
                        "numeroLote" => $this->data_specific_by_sector['numeroLote'],
                        "kilosNetosHumedos" => $this->data_specific_by_sector['kilosNetosHumedos'],
                        "humedadValor" => $this->data_specific_by_sector['humedadValor'],
                        "humedadPorcentaje" => $this->data_specific_by_sector['humedadPorcentaje'],
                        "mermaValor" => $this->data_specific_by_sector['mermaValor'],
                        "mermaPorcentaje" => $this->data_specific_by_sector['mermaPorcentaje'],
                        "kilosNetosSecos" => $this->data_specific_by_sector['kilosNetosSecos'],
                        "gastosRealizacion" => $this->data_specific_by_sector['gastosRealizacion'],
                        "monedaTransaccional" => $this->data_specific_by_sector['otrosDatos']->monedaTransaccional ?? null,
                        "fleteInternoUSD" => $this->data_specific_by_sector['otrosDatos']->fleteInternoUSD ?? null,
                        "valorFobFrontera" => $this->data_specific_by_sector['otrosDatos']->valorFobFrontera ?? null,
                        "valorPlata" => $this->data_specific_by_sector['otrosDatos']->valorPlata ?? null,
                        "valorFobFronteraBs" => $this->data_specific_by_sector['otrosDatos']->valorFobFronteraBs ?? null,
                        "liquidacionPreliminar" => (string) $this->data_specific_by_sector['liquidacion_preliminar'],
                        "iva" => (string) $this->data_specific_by_sector['iva'],
                    ]);
                case TypeDocumentSector::COMERCIAL_EXPORTACION:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "gastoTransporteNacional" => !empty($this->costosGastosNacionales['gastoTransporte']) ? (string) $this->costosGastosNacionales['gastoTransporte'] : "",
                        "gastoSeguroNacional" => !empty($this->costosGastosNacionales['gastoSeguro']) ? (string) $this->costosGastosNacionales['gastoSeguro'] : "",
                        "gastoTransporteInternacional" => !empty($this->costosGastosInternacionales['gastoTransporte']) ? (string) $this->costosGastosInternacionales['gastoTransporte'] : "",
                        "gastoSeguroInternacional" => !empty($this->costosGastosInternacionales['gastoSeguro']) ? (string) $this->costosGastosInternacionales['gastoSeguro'] : "",
                        "puertoDestino" => $this->data_specific_by_sector['puertoDestino'],
                        "paisDestino" => (string)$this->data_specific_by_sector['paisDestino'],
                        "lugarDestino" => $this->data_specific_by_sector['lugarDestino'],
                        "incotermDetalle" => $this->data_specific_by_sector['incoterm_detalle'],
                        "incoterm" => $this->data_specific_by_sector['incoterm'],
                        "totalGastosNacionalesFob" => $this->data_specific_by_sector['totalGastosNacionalesFob'],
                        "totalGastosInternacionales" => $this->data_specific_by_sector['totalGastosInternacionales'],
                        "numeroDescripcionPaquetesBultos" => $this->data_specific_by_sector['numeroDescripcionPaquetesBultos'],
                        "informacionAdicional" => $this->data_specific_by_sector['informacionAdicional'],
                        "costosGastosNacionales" => !empty($this->data_specific_by_sector['costosGastosNacionales']) ?  json_encode($this->data_specific_by_sector['costosGastosNacionales']) : "",
                        "costosGastosInternacionales" => !empty($this->data_specific_by_sector['costosGastosInternacionales']) ?  json_encode($this->data_specific_by_sector['costosGastosInternacionales']) : "",
                        "liquidacionPreliminar" => (string) $this->data_specific_by_sector['liquidacion_preliminar'],
                        "direccionComprador" => $this->data_specific_by_sector['direccionComprador'],

                    ]);
                case TypeDocumentSector::TELECOMUNICACIONES:
                    return array_merge($main, [
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
                case TypeDocumentSector::DEBITO_CREDITO:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "idFacturaOriginal" => (string)$this->factura_original_id_hashed,
                        "numeroNota" => (int) $this->numero_factura,
                        "numeroAutorizacionCuf" => (string) $this->numeroAutorizacionCuf, // cuf invoice ref
                        "montoDescuentoCreditoDebito" => (string) $this->montoDescuentoCreditoDebito,
                        "montoEfectivoCreditoDebito" => (string) $this->montoEfectivoCreditoDebito,
                        "idFacturaOriginal" => (string)$this->factura_original_id_hashed,
                    ]);
                case TypeDocumentSector::COMERCIAL_EXPORTACION_SERVICIOS:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                    ]);
                case TypeDocumentSector::NOTA_CONCILIACION:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "numeroFacturaOriginal" => isset($this->external_invoice_data) ? $this->external_invoice_data['numeroFacturaOriginal'] : null,
                        "montoTotalOriginal" => isset($this->external_invoice_data) ? $this->external_invoice_data['montoTotalOriginal'] : null,
                        "codigoControl" =>  isset($this->external_invoice_data) ? $this->external_invoice_data['codigoControl'] : null,
                        "debitoFiscalIva" =>    isset($this->debitoFiscalIva) ? $this->debitoFiscalIva : null,
                        "creditoFiscalIva" =>   isset($this->creditoFiscalIva) ? $this->creditoFiscalIva : null,
                        "fechaEmisionOriginal" =>   isset($this->external_invoice_data) ? $this->external_invoice_data['fechaEmisionOriginal'] : null,
                        "montoTotalConciliado" =>   isset($this->montoTotal) ? $this->montoTotal : null,
                    ]);
                case TypeDocumentSector::SEGUROS:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "ajusteAfectacionIva" => $this->ajusteAfectacionIva,
                    ]);
                case TypeDocumentSector::COMPRA_VENTA_BONIFICACIONES:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                    ]);
                case TypeDocumentSector::HIDROCARBUROS_NO_IEHD:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                    ]);
                case TypeDocumentSector::PRODUCTOS_ALCANZADOS_ICE:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "montoIceEspecifico" =>   isset($this->data_specific_by_sector['montoIceEspecifico']) ? (string) $this->data_specific_by_sector['montoIceEspecifico'] : '',
                        "montoIcePorcentual" =>   isset($this->data_specific_by_sector['montoIcePorcentual']) ? (string) $this->data_specific_by_sector['montoIcePorcentual'] : '',
                    ]);
                case TypeDocumentSector::SERVICIO_TURISTICO_HOSPEDAJE:
                    return array_merge($main, [
                        "montoTotalSujetoIva" => $this->montoTotalSujetoIva,
                        "tipoCambio" => round((float)$this->tipoCambio, 2),
                        "montoGiftCard" => (string)$this->montoGiftCard ?? null,
                        "razonSocialOperadorTurismo" => isset($this->data_specific_by_sector['razonSocialOperadorTurismo']) ? $this->data_specific_by_sector['razonSocialOperadorTurismo'] : '', 
                    ]);
                default:
                    return [];
                    break;
            }
        }    catch(Throwable $ex) {
            \Log::debug("error  file " . $ex->getFile(). " Line " . $ex->getLine(). " Message : " . $ex->getMessage() );
        }
    }
}
