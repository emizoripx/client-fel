<?php
namespace EmizorIpx\ClientFel\Services\Import;

use App\Repositories\ProductRepository;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use EmizorIpx\ClientFel\Repository\FelClientRepository;
use EmizorIpx\ClientFel\Repository\FelProductRepository;
use EmizorIpx\ClientFel\Services\BaseConnection;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

class CSVFelImport
{
    protected $entity_type;

    public function __construct($entity_type)
    {
        $this->entity_type = $entity_type;
    }

    public function createFelEntity( $data, $model ){

        \Log::debug("Create Fel Entity ". $this->entity_type);

        switch ($this->entity_type) {
            case 'product':
                $product_repo = new FelProductRepository();
                $fel_data = $this->prepareFelData($this->entity_type, $data);

                $product_repo->create($fel_data, $model);
                \Log::debug("Created Fel Data");

                break;
            case 'client':
                $client_repo = new FelClientRepository();
                $fel_data = $this->prepareFelData($this->entity_type, $data);

                $client_repo->create($fel_data, $model);
                \Log::debug("Created Fel Data");

                break;
            
            default:
                # code...
                break;
        }

    }

    public function prepareFelData($entity_type, $data){

        switch ($entity_type) {
            case 'product':
                \Log::debug("Prepare Data Products");
                return [
                    "codigo_unidad"                 => $data['product.fel_data.codigo_unidad'],
                    "nombre_unidad"                 => $data['product.fel_data.nombre_unidad'],
                    "codigo_actividad_economica"    => $data['product.fel_data.codigo_actividad_economica'],
                    "codigo_producto_sin"           => $data['product.fel_data.codigo_producto_sin'],
                    "codigoNandina"                 => $data['product.fel_data.codigo_nandina'],
                    "codigo"                        => $data['product.fel_data.codigo'],
                    "cantidadIce"                   => isset($data['product.fel_data.litros_por_item']) ? $data['product.fel_data.litros_por_item'] : "",
                    "marcaIce"                      => isset($data['product.fel_data.tiene_ice']) ? $data['product.fel_data.tiene_ice'] : 0,
                    "alicuotaEspecifica"            => isset($data['product.fel_data.ice_especifico']) ? $data['product.fel_data.ice_especifico'] : "0.00",
                    "alicuotaPorcentual"            => isset($data['product.fel_data.ice_porcentual']) ? $data['product.fel_data.ice_porcentual'] : "0.00",
                ];

                break;

            case 'client':
                \Log::debug("Prepare Data Client");
                return [
                    "type_document_id"      => $data['client.fel_data.tipo_documento'],
                    "document_number"       => $data['client.fel_data.document_number'],
                    "business_name"         => $data['client.fel_data.business_name'],
                    "complement"            => $data['client.fel_data.complement']
                ];

                break;
            
            
            default:
                # code...
                break;
        }

    }

    public static function convertToCsv( $errors ){

        $file_name = 'Reporte_Errores.csv';

		$handle = fopen(public_path($file_name), 'w+');
		fputcsv($handle, array('tipo', 'data', 'errors'));

		foreach ($errors as $key => $type_entity) {
			foreach ( $type_entity as $record ){

				fputcsv($handle, array($key, json_encode($record[$key]), $record['error']));
			}
		}

        return $file_name;
        
    }

    
}