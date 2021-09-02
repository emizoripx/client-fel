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
        try {

            switch ($this->entity) {
                case 'client':
                    $this->fel_data_parsed = [
                        "type_document_id" => $fel_data['type_document_id'],
                        "document_number" => $fel_data['document_number'],
                        "business_name" => $fel_data['business_name'],
                        "complement" => $fel_data['complement'] ?? null
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
                    ];
                    break;
                case 'invoice':
                    $this->fel_data_parsed = [
                        "activity_id" => $fel_data['codigoActividad'],
                        "caption_id" => $fel_data['codigoLeyenda'],
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
                        // FACTURA VENTA MINERALES
                        "iva" => !empty($fel_data['iva']) ? $fel_data['iva'] : 0,
                        "liquidacionPreliminar" => !empty($fel_data['liquidacionPreliminar']) ? $fel_data['liquidacionPreliminar'] : 0,
                        "precioConcentradoBs" => !empty($fel_data['precioConcentradoBs']) ? $fel_data['precioConcentradoBs'] : 0,
                        "precioConcentrado" => !empty($fel_data['precioConcentrado']) ? $fel_data['precioConcentrado'] : 0,

                        // COMERCIA EXPORTACION
                        "incoterm_detalle" => !empty($fel_data['incotermDetalle']) ? $fel_data['incotermDetalle'] : "",
                        "lugarDestino" => !empty($fel_data['lugarDestino']) ? $fel_data['lugarDestino'] : "",
                        "gastoTransporteNacional" => !empty($fel_data['gastoTransporteNacional']) ? $fel_data['gastoTransporteNacional'] : '0',
                        "gastoSeguroNacional" => !empty($fel_data['gastoSeguroNacional']) ? $fel_data['gastoSeguroNacional'] : '0',
                        "totalGastosNacionalesFob" => !empty($fel_data['totalGastosNacionalesFob']) ? $fel_data['totalGastosNacionalesFob'] : 0,
                        "gastoTransporteInternacional" => !empty($fel_data['gastoTransporteInternacional']) ? $fel_data['gastoTransporteInternacional'] : '0',
                        "gastoSeguroInternacional" => !empty($fel_data['gastoSeguroInternacional']) ? $fel_data['gastoSeguroInternacional'] : '0',

                        "totalGastosInternacionales" => !empty($fel_data['totalGastosInternacionales']) ? $fel_data['totalGastosInternacionales'] : 0,

                        "numeroDescripcionPaquetesBultos" => !empty($fel_data['numeroDescripcionPaquetesBultos']) ? $fel_data['numeroDescripcionPaquetesBultos'] : "",
                        "informacionAdicional" => !empty($fel_data['informacionAdicional']) ? $fel_data['informacionAdicional'] : "",
                        "montoGeneral" => !empty($fel_data['montoGeneral']) ? $fel_data['montoGeneral'] : 0,
                        "montoTotal" => !empty($fel_data['montoTotal']) ? $fel_data['montoTotal'] : 0,
                        "montoGeneralBs" => !empty($fel_data['montoGeneralBs']) ? $fel_data['montoGeneralBs'] : 0,

                        // NOTA-DEBITO-CREDITO

                        "numeroAutorizacionCuf" => !empty($fel_data['numeroAutorizacionCuf']) ? $fel_data['numeroAutorizacionCuf'] : null,
                        "montoDescuentoCreditoDebito" => !empty($fel_data['montoDescuentoCreditoDebito']) ? $fel_data['montoDescuentoCreditoDebito'] : null,
                        "montoEfectivoCreditoDebito" => !empty($fel_data['montoEfectivoCreditoDebito']) ? $fel_data['montoEfectivoCreditoDebito'] : null,
                    ];
                    break;
                
            }

        } catch (Throwable $ex) {
            
            bitacora_error("BaseRepository", $ex->getMessage());
        }
    }
}
