<?php

namespace EmizorIpx\ClientFel\Repository;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use Exception;
use Throwable;

class BaseRepository
{
    protected $fel_data_parsed;

    protected $entity;

    protected function setEntity($value){
        $this->entity = $value;
    }


    protected function parseFelData($fel_data)
    {
        \Log::debug("datos feldata : " . json_encode($fel_data));
        try {

            switch ($this->entity) {
                case 'client':
                    $this->fel_data_parsed = [
                        "type_document_id" => $fel_data['type_document_id'],
                        "document_number" => $fel_data['document_number'],
                        "business_name" => $fel_data['business_name'],
                        "complement" => $fel_data['complement'] ?? null,
                        "codigoExcepcion" => $fel_data['codigo_excepcion'] ?? null
                    ];
                    break;
                case 'product':
                    $this->fel_data_parsed = [
                        "unit_id"           => $fel_data['codigo_unidad'],
                        "unit_name"         => $fel_data['nombre_unidad'],
                        "activity_id"       => $fel_data['codigo_actividad_economica'],
                        "product_sin_id"    => $fel_data['codigo_producto_sin'],
                        "codigo_nandina" => !empty($fel_data['codigoNandina']) ? $fel_data['codigoNandina'] : "",
                        "codigo_producto" => !empty($fel_data['codigo']) ? $fel_data['codigo'] : "",
                        "marcaIce" => !empty($fel_data['marcaIce']) ? $fel_data['marcaIce'] : null,
                        "alicuotaEspecifica" => !empty($fel_data['alicuotaEspecifica']) ? $fel_data['alicuotaEspecifica'] : null,
                        "alicuotaPorcentual" => !empty($fel_data['alicuotaPorcentual']) ? $fel_data['alicuotaPorcentual'] : null,
                        "cantidadIce" => !empty($fel_data['cantidadIce']) ? $fel_data['cantidadIce'] : null,
                    ];
                    break;
                case 'invoice':
                    $this->fel_data_parsed = [
                        "facturaTicket" => isset($fel_data['facturaTicket']) ? $fel_data['facturaTicket'] : null,
                        "nombreRazonSocial" =>isset($fel_data['nombreRazonSocial'])? $fel_data['nombreRazonSocial'] : null,
                        "codigoTipoDocumentoIdentidad" =>isset($fel_data['codigoTipoDocumentoIdentidad'])? $fel_data['codigoTipoDocumentoIdentidad'] : null,
                        "numeroDocumento" =>isset($fel_data['numeroDocumento'])? $fel_data['numeroDocumento'] : null,
                        "complemento" =>isset($fel_data['complemento'])? $fel_data['complemento'] : null,
                        "fechaDeEmision" =>isset($fel_data['fechaDeEmision'])? $fel_data['fechaDeEmision'] : null,
                        "numeroFactura" => isset($fel_data['numeroFactura'])? $fel_data['numeroFactura'] : null,
                        "activity_id" => isset($fel_data['codigoActividad']) ? $fel_data['codigoActividad']: 0,
                        "caption_id" => isset($fel_data['codigoLeyenda']) ? $fel_data['codigoLeyenda'] : null,
                        "payment_method_id" => $fel_data['codigoMetodoPago'],
                        "tipo_cambio" => !empty($fel_data['tipoCambio']) ? $fel_data['tipoCambio'] : 1,
                        "codigo_moneda" => !empty($fel_data['codigoMoneda']) ? $fel_data['codigoMoneda'] : 1,
                        'type_document_sector_id' => !empty($fel_data['sector_document_type_id']) ? $fel_data['sector_document_type_id'] : 1,
                        'numero_tarjeta' => !empty($fel_data['numeroTarjeta']) ? $fel_data['numeroTarjeta']: null,
                        'extras' => !empty($fel_data['extras']) ? $fel_data['extras'] : null,
                        "direccionComprador" => !empty($fel_data["direccionComprador"]) ?  $fel_data["direccionComprador"] : "",
                        "ruex" => !empty($fel_data["ruex"]) ?  $fel_data["ruex"] : "",
                        "nim" => !empty($fel_data["nim"]) ?  $fel_data["nim"] : "",
                        "concentradoGranel" => !empty($fel_data["concentradoGranel"]) ?  $fel_data["concentradoGranel"] : "",
                        "origen" => !empty($fel_data["origen"]) ?  $fel_data["origen"] : "",
                        "puertoTransito" => !empty($fel_data["puertoTransito"]) ?  $fel_data["puertoTransito"] : "",
                        "puertoDestino" => !empty($fel_data["puertoDestino"]) ?  $fel_data["puertoDestino"] : "",
                        "paisDestino" => !empty($fel_data["paisDestino"]) ?  $fel_data["paisDestino"] : "",
                        "incoterm" => !empty($fel_data["incoterm"]) ?  $fel_data["incoterm"] : "",
                        "tipoCambioANB" => !empty($fel_data["tipoCambioANB"]) ?  $fel_data["tipoCambioANB"] : "",
                        "numeroLote" => !empty($fel_data["numeroLote"]) ?  $fel_data["numeroLote"] : "",
                        "kilosNetosHumedos" => !empty($fel_data["kilosNetosHumedos"]) ?  $fel_data["kilosNetosHumedos"] : "",
                        "humedadPorcentaje" => !empty($fel_data["humedadPorcentaje"]) ?  $fel_data["humedadPorcentaje"] : "",
                        "humedadValor" => !empty($fel_data["humedadValor"]) ?  $fel_data["humedadValor"] : "",
                        "mermaPorcentaje" => !empty($fel_data["mermaPorcentaje"]) ?  $fel_data["mermaPorcentaje"] : "",
                        "mermaValor" => !empty($fel_data["mermaValor"]) ?  $fel_data["mermaValor"] : "",
                        "kilosNetosSecos" => !empty($fel_data["kilosNetosSecos"]) ?  $fel_data["kilosNetosSecos"] : "",
                        "gastosRealizacion" => !empty($fel_data["gastosRealizacion"]) ?  $fel_data["gastosRealizacion"] : "",
                        "valorFobFrontera" => !empty($fel_data['valorFobFrontera']) ? $fel_data['valorFobFrontera'] : "",
                        "fleteInternoUSD" => !empty($fel_data['fleteInterno']) ? $fel_data['fleteInterno'] : "",
                        "valorPlata" => !empty($fel_data['valorPlata']) ? $fel_data['valorPlata'] : "",
                        "valorFobFronteraBs" => !empty($fel_data['valorFobFronteraBs']) ? $fel_data['valorFobFronteraBs'] : "",
                        "monedaTransaccional" => !empty($fel_data['monedaTransaccional']) ? $fel_data['monedaTransaccional'] : "",
                        "codigoPuntoVenta" => !empty($fel_data['codigo_pos']) ? $fel_data['codigo_pos'] : "",
                        "codigoSucursal" => !empty($fel_data['codigo_sucursal']) ? $fel_data['codigo_sucursal'] : 0,
                        "codigoExcepcion" => !empty($fel_data['codigoExcepcion']) ? $fel_data['codigoExcepcion'] : null,
                        "montoGiftCard" => !empty($fel_data['montoGiftCard']) ? $fel_data['montoGiftCard'] : null,
                        "descuentoAdicional" => !empty($fel_data['descuentoAdicional']) ? $fel_data['descuentoAdicional'] : null,
                        "cafc" => !empty($fel_data['cafc']) ? $fel_data['cafc'] : null,

                        "pesoBrutoGr" => !empty($fel_data['pesoBrutoGr']) ? $fel_data['pesoBrutoGr'] : 0,
                        "pesoBrutoKg" => !empty($fel_data['pesoBrutoKg']) ? $fel_data['pesoBrutoKg'] : 0,
                        "pesoNetoGr" => !empty($fel_data['pesoNetoGr']) ? $fel_data['pesoNetoGr'] : 0,
                        "numeroContrato" => !empty($fel_data['numeroContrato']) ? $fel_data['numeroContrato'] : "",
                        // FACTURA VENTA MINERALES
                        "iva" => !empty($fel_data['iva']) ? $fel_data['iva'] : 0,
                        "liquidacionPreliminar" => !empty($fel_data['liquidacionPreliminar']) ? $fel_data['liquidacionPreliminar'] : 0,
                        "precioConcentradoBs" => !empty($fel_data['precioConcentradoBs']) ? $fel_data['precioConcentradoBs'] : 0,
                        "precioConcentrado" => !empty($fel_data['precioConcentrado']) ? $fel_data['precioConcentrado'] : 0,

                        // COMERCIA EXPORTACION
                        "incoterm_detalle" => !empty($fel_data['incotermDetalle']) ? $fel_data['incotermDetalle'] : "",
                        "lugarDestino" => !empty($fel_data['lugarDestino']) ? $fel_data['lugarDestino'] : "",

                        "costosGastosNacionales" =>!empty($fel_data['costosGastosNacionales']) ? $fel_data['costosGastosNacionales'] : [],

                        // "gastoTransporteNacional" => !empty($fel_data['gastoTransporteNacional']) ? $fel_data['gastoTransporteNacional'] : '0',
                        // "gastoSeguroNacional" => !empty($fel_data['gastoSeguroNacional']) ? $fel_data['gastoSeguroNacional'] : '0',
                       
                        "totalGastosNacionalesFob" => !empty($fel_data['totalGastosNacionalesFob']) ? $fel_data['totalGastosNacionalesFob'] : 0,

                        "costosGastosInternacionales" => !empty($fel_data['costosGastosInternacionales']) ? $fel_data['costosGastosInternacionales'] : [],

                        // "gastoTransporteInternacional" => !empty($fel_data['gastoTransporteInternacional']) ? $fel_data['gastoTransporteInternacional'] : '0',
                        // "gastoSeguroInternacional" => !empty($fel_data['gastoSeguroInternacional']) ? $fel_data['gastoSeguroInternacional'] : '0',

                        "totalGastosInternacionales" => !empty($fel_data['totalGastosInternacionales']) ? $fel_data['totalGastosInternacionales'] : 0,

                        "numeroDescripcionPaquetesBultos" => !empty($fel_data['numeroDescripcionPaquetesBultos']) ? $fel_data['numeroDescripcionPaquetesBultos'] : "",
                        "informacionAdicional" => !empty($fel_data['informacionAdicional']) ? $fel_data['informacionAdicional'] : "",
                        "montoGeneral" => !empty($fel_data['montoGeneral']) ? $fel_data['montoGeneral'] : 0,
                        "montoTotal" => !empty($fel_data['montoTotal']) ? $fel_data['montoTotal'] : 0,
                        "montoTotalSujetoIva" => !empty($fel_data['montoTotalSujetoIva']) ? $fel_data['montoTotalSujetoIva'] : 0,
                        "montoGeneralBs" => !empty($fel_data['montoGeneralBs']) ? $fel_data['montoGeneralBs'] : 0,

                        // NOTA-DEBITO-CREDITO

                        "numeroAutorizacionCuf" => !empty($fel_data['numeroAutorizacionCuf']) ? $fel_data['numeroAutorizacionCuf'] : null,
                        "montoDescuentoCreditoDebito" => !empty($fel_data['montoDescuentoCreditoDebito']) ? $fel_data['montoDescuentoCreditoDebito'] : null,
                        "montoEfectivoCreditoDebito" => !empty($fel_data['montoEfectivoCreditoDebito']) ? $fel_data['montoEfectivoCreditoDebito'] : null,

                        // SECTOR EDUCATIVO
                        "nombreEstudiante" => !empty($fel_data['nombreEstudiante']) ? $fel_data['nombreEstudiante'] : "NOMBRE ESTUDIANTE",
                        "periodoFacturado" => !empty($fel_data['periodoFacturado']) ? $fel_data['periodoFacturado'] : " PERIODO FACTURADO",

                        // HIDROCARBUROS
                        "ciudad" => !empty($fel_data['ciudad']) ? $fel_data['ciudad'] : null,
                        "nombrePropietario" => !empty($fel_data['nombrePropietario']) ? $fel_data['nombrePropietario'] : null,
                        "nombreRepresentanteLegal" => !empty($fel_data['nombreRepresentanteLegal']) ? $fel_data['nombreRepresentanteLegal'] : null,
                        "condicionPago" => !empty($fel_data['condicionPago']) ? $fel_data['condicionPago'] : null,
                        "periodoEntrega" => !empty($fel_data['periodoEntrega']) ? $fel_data['periodoEntrega'] : null,
                        "montoIehd" => !empty($fel_data['montoIehd']) ? $fel_data['montoIehd'] : null,

                        // SERVICIOS BASICOS

                        "mes" => !empty($fel_data['mes']) ? $fel_data['mes'] : null,
                        "gestion" => !empty($fel_data['gestion']) ? $fel_data['gestion'] : null,
                        "ciudad" => !empty($fel_data['ciudad']) ? $fel_data['ciudad'] : null,
                        "zona" => !empty($fel_data['zona']) ? $fel_data['zona'] : null,
                        "numeroMedidor" => !empty($fel_data['numeroMedidor']) ? $fel_data['numeroMedidor'] : null,
                        "domicilioCliente" => !empty($fel_data['domicilioCliente']) ? $fel_data['domicilioCliente'] : null,
                        "consumoPeriodo" => !empty($fel_data['consumoPeriodo']) ? $fel_data['consumoPeriodo'] : null,
                        "beneficiarioLey1886" => !empty($fel_data['beneficiarioLey1886']) ? $fel_data['beneficiarioLey1886'] : null,
                        "montoDescuentoLey1886" => !empty($fel_data['montoDescuentoLey1886']) ? $fel_data['montoDescuentoLey1886'] : null,
                        "montoDescuentoTarifaDignidad" => !empty($fel_data['montoDescuentoTarifaDignidad']) ? $fel_data['montoDescuentoTarifaDignidad'] : null,
                        "tasaAseo" => !empty($fel_data['tasaAseo']) ? $fel_data['tasaAseo'] : null,
                        "tasaAlumbrado" => !empty($fel_data['tasaAlumbrado']) ? $fel_data['tasaAlumbrado'] : null,
                        "ajusteNoSujetoIva" => !empty($fel_data['ajusteNoSujetoIva']) ? $fel_data['ajusteNoSujetoIva'] : null,
                        "detalleAjusteNoSujetoIva" => !empty($fel_data['detalleAjusteNoSujetoIva']) ? $fel_data['detalleAjusteNoSujetoIva'] : null,
                        "ajusteSujetoIva" => !empty($fel_data['ajusteSujetoIva']) ? $fel_data['ajusteSujetoIva'] : null,
                        "detalleAjusteSujetoIva" => !empty($fel_data['detalleAjusteSujetoIva']) ? $fel_data['detalleAjusteSujetoIva'] : null,
                        "otrosPagosNoSujetoIva" => !empty($fel_data['otrosPagosNoSujetoIva']) ? $fel_data['otrosPagosNoSujetoIva'] : null,
                        "detalleOtrosPagosNoSujetoIva" => !empty($fel_data['detalleOtrosPagosNoSujetoIva']) ? $fel_data['detalleOtrosPagosNoSujetoIva'] : null,
                        "otrasTasas" => !empty($fel_data['otrasTasas']) ? $fel_data['otrasTasas'] : null,

                        //hoteles 
                        "cantidadHuespedes" => !empty($fel_data["cantidadHuespedes"]) ? $fel_data["cantidadHuespedes"] : null,
                        "cantidadHabitaciones" => !empty($fel_data["cantidadHabitaciones"]) ? $fel_data["cantidadHabitaciones"] : null,
                        "cantidadMayores" => !empty($fel_data["cantidadMayores"]) ? $fel_data["cantidadMayores"] : null,
                        "cantidadMenores" => !empty($fel_data["cantidadMenores"]) ? $fel_data["cantidadMenores"] : null,
                        "fechaIngresoHospedaje" => !empty($fel_data["fechaIngresoHospedaje"]) ? $fel_data["fechaIngresoHospedaje"] : null,

                        // Comercializacion Hidrocarburos
                        "placaVehiculo" => !empty($fel_data["placaVehiculo"]) ? $fel_data["placaVehiculo"] : null,
                        "tipoEnvase" => !empty($fel_data["tipoEnvase"]) ? $fel_data["tipoEnvase"] : null,
                        "codigoAutorizacionSC" => !empty($fel_data["codigoAutorizacionSC"]) ? $fel_data["codigoAutorizacionSC"] : null,
                        "observacion" => !empty($fel_data["observacion"]) ? $fel_data["observacion"] : null,
                        "montoVale" => !empty($fel_data["montoVale"]) ? $fel_data["montoVale"] : null,

                        // Telecomunicaciones
                        "nitConjunto" => !empty($fel_data["nitConjunto"]) ? $fel_data["nitConjunto"] : null,

                        //otros datos, dinamically
                        "otrosDatos" => !empty($fel_data["otrosDatos"]) ? json_decode($fel_data["otrosDatos"]) : [],

                        // typeDocument for different types of documents
                        "typeDocument" =>isset($fel_data["typeDocument"]) ? $fel_data["typeDocument"] : 0,

                        // seguros
                        "ajusteAfectacionIva" => !empty($fel_data["ajusteAfectacionIva"]) ? $fel_data["ajusteAfectacionIva"] : 0,

                        // nota conciliacion
                        "creditoFiscalIva" => !empty($fel_data["creditoFiscalIva"]) ? $fel_data["creditoFiscalIva"] : 0,
                        "debitoFiscalIva" => !empty($fel_data["debitoFiscalIva"]) ? $fel_data["debitoFiscalIva"] : 0,
                        "line_items_nc" => !empty($fel_data["line_items_nc"]) ? $fel_data["line_items_nc"] : [],
                        "numeroFacturaOriginal" => !empty($fel_data["numeroFacturaOriginal"])? $fel_data["numeroFacturaOriginal"] : "",
                        "numeroAutorizacionCuf" => !empty($fel_data["numeroAutorizacionCuf"])? $fel_data["numeroAutorizacionCuf"] : "",
                        "codigoControl" => !empty($fel_data["codigoControl"])? $fel_data["codigoControl"] : "",
                        "fechaEmisionOriginal" => !empty($fel_data["fechaEmisionOriginal"])? $fel_data["fechaEmisionOriginal"] : "",
                        "montoTotalOriginal" => !empty($fel_data["montoTotalOriginal"])? $fel_data["montoTotalOriginal"] : "",
                        "montoTotalConciliado" => !empty($fel_data["montoTotalConciliado"])? $fel_data["montoTotalConciliado"] : "",


                        // zona franca
                        "numeroParteRecepcion" => !empty($fel_data["numeroParteRecepcion"]) ? $fel_data["numeroParteRecepcion"] : null,
                        
                        // CLINICAS
                        "modalidadServicio" => !empty($fel_data["modalidadServicio"]) ? $fel_data["modalidadServicio"] : null,

                        "montoIceEspecifico" => !empty($fel_data["montoIceEspecifico"]) ? $fel_data["montoIceEspecifico"] : null,
                        "montoIcePorcentual" => !empty($fel_data["montoIcePorcentual"]) ? $fel_data["montoIcePorcentual"] : null,

                        //ALQUILERES
                        "valorUFV" => !empty($fel_data['valorUFV']) ? $fel_data["valorUFV"] : null,
                        "agencia" => !empty($fel_data['agencia']) ? $fel_data["agencia"] : null,
                        "id_agencia" => !empty($fel_data['id_agencia']) ? $fel_data["id_agencia"] : null,
                        "poliza" => !empty($fel_data['poliza']) ? $fel_data["poliza"] : null,

                        // TICKETS
                        "turno" => !empty($fel_data['turno']) ? $fel_data["turno"] : null,
                        "idFacturaOriginal" => !empty($fel_data['idFacturaOriginal']) ? $fel_data["idFacturaOriginal"] : null

                    ];
                    break;
                
            }

        } catch (Throwable $ex) {
            \Log::emergency("File: " . $ex->getFile() . " Line: " . $ex->getLine() . " Message: " . $ex->getMessage());
            bitacora_error("BaseRepository", $ex->getMessage());
        }
    }
}
