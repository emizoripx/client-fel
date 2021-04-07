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


            // $fel_data_decoded = json_decode($fel_data);

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
                        "payment_method_id" => $fel_data['codigoMetodoPago']
                    ];
                    break;
                
            }

            // if (property_exists($fel_data_decoded, 'client')) {

            //     $this->fel_data_parsed = [
            //         "type_document_id" => $fel_data_decoded->client->type_document_id,
            //         "complement" => $fel_data_decoded->client->complement ?? null
            //     ];
            // }

            // if (property_exists($fel_data_decoded, 'product')) {

            //     $this->fel_data_parsed = [
            //         "unit_id"           => $fel_data_decoded->product->unit_id,
            //         "unit_name"         => $fel_data_decoded->product->unit_name,
            //         "activity_id"       => $fel_data_decoded->product->activity_id,
            //         "product_sin_id"    => $fel_data_decoded->product->product_sin_id
            //     ];
            // }

            // if (property_exists($fel_data_decoded, 'invoice')) {

            //     $this->fel_data_parsed = [
            //         "activity_id" => $fel_data_decoded->invoice->activity_id,
            //         "caption_id" => $fel_data_decoded->invoice->caption_id,
            //         "payment_method_id" => $fel_data_decoded->invoice->payment_method_id
            //     ];
            // }
        } catch (Throwable $ex) {
            
            bitacora_error("BaseRepository", $ex->getMessage());
        }
    }
}
