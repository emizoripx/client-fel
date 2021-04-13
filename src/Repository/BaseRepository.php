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
                        "product_sin_id"    => $fel_data['codigo_producto_sin']
                    ];
                    break;
                case 'invoice':
                    $this->fel_data_parsed = [
                        "activity_id" => $fel_data['codigoActividad'],
                        "caption_id" => $fel_data['codigoLeyenda'],
                        "payment_method_id" => $fel_data['codigoMetodoPago'],
                        "tipo_cambio" => !empty($fel_data['tipoCambio']) ? $fel_data['tipoCambio'] : 1,
                        "codigo_moneda" => !empty($fel_data['codigoMoneda']) ? $fel_data['codigoMoneda'] : 1,
                        'type_document_sector_id' => !empty($fel_data['typeDocumentSectorId']) ? $fel_data['typeDocumentSectorId'] : 1,
                        'numero_tarjeta' => !empty($fel_data['numeroTarjeta']) ? $fel_data['numeroTarjeta']: null,
                        'extras' => !empty($fel_data['extras']) ? $fel_data['extras'] : null
                    ];
                    break;
                
            }

        } catch (Throwable $ex) {
            
            bitacora_error("BaseRepository", $ex->getMessage());
        }
    }
}
