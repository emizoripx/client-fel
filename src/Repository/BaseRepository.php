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
                    ];
                    break;
                
            }

        } catch (Throwable $ex) {
            
            bitacora_error("BaseRepository", $ex->getMessage());
        }
    }
}
