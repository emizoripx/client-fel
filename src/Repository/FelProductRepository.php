<?php
namespace EmizorIpx\ClientFel\Repository;

use Carbon\Carbon;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use EmizorIpx\ClientFel\Repository\Interfaces\RepoInterface;
use Exception;

class FelProductRepository extends BaseRepository implements RepoInterface{



    public function create($fel_data, $model) 
    {
        
        bitacora_info("FelProductRepository:create",json_encode($fel_data));

        try {

            $this->setEntity('product');
            $this->parseFelData($fel_data);

            \Log::debug($model->product_key);
            $input = [
                "company_id" => $model->company_id,
                "id_origin" => $model->id,
                "codigo_producto" => $model->product_key,
                "codigo_producto_sin" => $this->fel_data_parsed['product_sin_id'],
                "codigo_actividad_economica" => $this->fel_data_parsed['activity_id'],
                "codigo_unidad" => $this->fel_data_parsed['unit_id'],
                "nombre_unidad" => $this->fel_data_parsed['unit_name'],
                "created_at" => Carbon::now()
            ];

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
        
            $input = [
                "codigo_producto_sin" => $this->fel_data_parsed['product_sin_id'],
                "codigo_actividad_economica" => $this->fel_data_parsed['activity_id'],
                "codigo_unidad" => $this->fel_data_parsed['unit_id'],
                "nombre_unidad" => $this->fel_data_parsed['unit_name'],
                "updated_at" => Carbon::now()
            ];

                $product = FelSyncProduct::where('id_origin',$model->id)->first();
                
                if (!is_null($product)) {

                    $product->update( $input );
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


}