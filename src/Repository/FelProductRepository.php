<?php
namespace EmizorIpx\ClientFel\Repository;

use App\Factory\ProductFactory;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use EmizorIpx\ClientFel\Models\Parametric\Unit;
use EmizorIpx\ClientFel\Repository\Interfaces\RepoInterface;
use Exception;

class FelProductRepository extends BaseRepository implements RepoInterface{



    public function create($fel_data, $model) 
    {
        
        bitacora_info("FelProductRepository:create",json_encode($fel_data));

        try {

            $this->setEntity('product');
            $this->parseFelData($fel_data);

            $additional_data = [];

            $input = [
                "company_id" => $model->company_id,
                "id_origin" => $model->id,
                "codigo_producto" => $this->fel_data_parsed['codigo_producto'],
                "codigo_producto_sin" => $this->fel_data_parsed['product_sin_id'],
                "codigo_actividad_economica" => $this->fel_data_parsed['activity_id'],
                "codigo_unidad" => $this->fel_data_parsed['unit_id'],
                "nombre_unidad" => $this->fel_data_parsed['unit_name'],
                "codigo_nandina" => $this->fel_data_parsed['codigo_nandina'],
                "created_at" => Carbon::now()
            ];

            if( !is_null($this->fel_data_parsed['marcaIce']) ){
                $additional_data = array_merge($additional_data, ['marcaIce' => intval($this->fel_data_parsed['marcaIce'])]);
            }
            if( !is_null($this->fel_data_parsed['alicuotaEspecifica']) ){
                $additional_data = array_merge($additional_data, ['alicuotaEspecifica' => $this->fel_data_parsed['alicuotaEspecifica'] ]);
            }
            if( !is_null($this->fel_data_parsed['alicuotaPorcentual']) ){
                $additional_data = array_merge($additional_data, ['alicuotaPorcentual' => $this->fel_data_parsed['alicuotaPorcentual'] ]);
            }
            if( !is_null($this->fel_data_parsed['cantidadIce']) ){
                $additional_data = array_merge($additional_data, ['cantidadIce' => $this->fel_data_parsed['cantidadIce'] ]);
            }

            if( ! empty($additional_data) ) {
                $input = array_merge( $input, [
                    'additional_data' => $additional_data
                ] );
            }

            \Log::debug("Create Products: " . json_encode($additional_data));

            FelSyncProduct::create($input);

        } catch (Exception $ex) {
            bitacora_error("FelProductRepository:create", $ex->getMessage());
        }

    }

    public function update($fel_data, $model)
    {
        bitacora_info("FelProductRepository:update", json_encode($fel_data));

        try {

            $this->setEntity('product');
            $this->parseFelData($fel_data);

            $additional_data = [];
        
            $input = [
                "codigo_producto" => $this->fel_data_parsed['codigo_producto'],
                "codigo_nandina" => $this->fel_data_parsed['codigo_nandina'],
                "codigo_producto_sin" => $this->fel_data_parsed['product_sin_id'],
                "codigo_actividad_economica" => $this->fel_data_parsed['activity_id'],
                "codigo_unidad" => $this->fel_data_parsed['unit_id'],
                "nombre_unidad" => $this->fel_data_parsed['unit_name'],
                "updated_at" => Carbon::now()
            ];

            if( !is_null($this->fel_data_parsed['marcaIce']) ){
                $additional_data = array_merge($additional_data, ['marcaIce' => $this->fel_data_parsed['marcaIce'] ]);
            }
            if( !is_null($this->fel_data_parsed['alicuotaEspecifica']) ){
                $additional_data = array_merge($additional_data, ['alicuotaEspecifica' => $this->fel_data_parsed['alicuotaEspecifica'] ]);
            }
            if( !is_null($this->fel_data_parsed['alicuotaPorcentual']) ){
                $additional_data = array_merge($additional_data, ['alicuotaPorcentual' => $this->fel_data_parsed['alicuotaPorcentual'] ]);
            }
            if( !is_null($this->fel_data_parsed['cantidadIce']) ){
                $additional_data = array_merge($additional_data, ['cantidadIce' => $this->fel_data_parsed['cantidadIce'] ]);
            }

            if( !empty($additional_data) ) {
                $input = array_merge( $input, [
                    'additional_data' => $additional_data
                ] );
            }

                $product = FelSyncProduct::where('id_origin',$model->id)->first();
                
                if (!is_null($product)) {

                    $product->update( $input );
                } else {
                    $this->create($fel_data, $model);
                }

        } catch (Exception $ex) {
            bitacora_error("FelProductRepository:update", $ex->getMessage());
        }
    }

    public function delete($model)
    {
        bitacora_info("FelProductRepository:delete", "");

        try {

            $product = FelSyncProduct::where('id_origin', $model->id)->first();

            if (!is_null($product)) {

                $product->delete();
            }
                            

        } catch (Exception $ex) {
            bitacora_error("FelProductRepository:delete", $ex->getMessage());
        }
    }
    public function restore($model)
    {
        bitacora_info("FelProductRepository:restore", "");

        try {

            $product = FelSyncProduct::withTrashed()->where('id_origin', $model->id)->first();

            if (!is_null($product)) {

                $product->restore();
            }
                            

        } catch (Exception $ex) {
            bitacora_error("FelProductRepository:restore", $ex->getMessage());
        }
    }


    public function createProduct( $data, $company_id, $user_id ) {

        \Log::debug("Create new product code: " . $data['product_code']);

        $this->validateData($data);

        $product_repo = new ProductRepository();

        $product = $product_repo->save($data, ProductFactory::create($company_id, $user_id));

        $fel_data = $this->prepareFelData($data);

        $this->setEntity('product');
        $this->parseFelData($fel_data);

        $input = [
            "company_id" => $product->company_id,
            "id_origin" => $product->id,
            "codigo_producto" => $this->fel_data_parsed['codigo_producto'],
            "codigo_producto_sin" => $this->fel_data_parsed['product_sin_id'],
            "codigo_actividad_economica" => $this->fel_data_parsed['activity_id'],
            "codigo_unidad" => $this->fel_data_parsed['unit_id'],
            "nombre_unidad" => strtoupper(Unit::getUnitDescription($this->fel_data_parsed['unit_id'])),
            "codigo_nandina" => $this->fel_data_parsed['codigo_nandina'],
            "created_at" => Carbon::now()
        ];

        $fel_product = FelSyncProduct::create($input);
        
        return $fel_product;

    }

    public function validateData( $data ) {

        if( !isset($data['product_code']) ) {
            throw new Exception('product_code requerido');
        }

        if( !isset($data['product_key']) ) {
            throw new Exception('product_key requerido');
        }

        if( !isset($data['notes']) ) {
            throw new Exception('notes requerido');
        }

        if( !isset($data['price']) ) {
            throw new Exception('price requerido');
        }

        if( !isset($data['activity_code']) ) {
            throw new Exception('activity_code requerido');
        }

        if( !isset($data['unit_code']) ) {
            throw new Exception('unit_code requerido');
        }

        // if( !isset($data['unit_name']) ) {
        //     throw new Exception('unit_name requerido');
        // }

        if( !isset($data['sin_product_code']) ) {
            throw new Exception('sin_product_code requerido');
        }

        
    }

    public function prepareFelData( $data ) {

        return [
            "codigo_unidad"                 => $data['unit_code'],
            "nombre_unidad"                 => isset($data['unit_name']) ? $data['unit_name'] : '',
            "codigo_actividad_economica"    => $data['activity_code'],
            "codigo_producto_sin"           => $data['sin_product_code'],
            "codigoNandina"                 => isset($data['nandina_code']) ? $data['nandina_code'] : '' ,
            "codigo"                        => $data['product_code']
        ];

    }

}