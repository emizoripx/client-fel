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

            $this->parseFelData($fel_data);

            $input = [
                "company_id" => $model->company_id,
                "id_origin" => $model->id,
                "codigo_producto" => $model->id,
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

            $this->parseFelData($fel_data);
        
            $input = [
                "codigo_producto_sin" => $this->fel_data_parsed['product_sin_id'],
                "codigo_actividad_economica" => $this->fel_data_parsed['activity_id'],
                "codigo_unidad" => $this->fel_data_parsed['unit_id'],
                "nombre_unidad" => $this->fel_data_parsed['unit_name'],
                "updated_at" => Carbon::now()
            ];

                FelSyncProduct::where('id_origin',$model->id)
                                ->first()
                                ->update( $input );

        } catch (Exception $ex) {
            bitacora_error("FelProductRepository:update", $ex->getMessage());
        }
    }

    public function delete($model)
    {
        bitacora_info("FelProductRepository:delete", "");

        try {

            FelSyncProduct::where('id_origin', $model->id)
                            ->first()
                            ->delete();

        } catch (Exception $ex) {
            bitacora_error("FelProductRepository:delete", $ex->getMessage());
        }
    }


}