<?php

namespace EmizorIpx\ClientFel\Observers;

use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Services\Products\Products;

class ProductFelObserver
{
    public function created($model) 
    {
        
        $access_token = FelClientToken::getTokenByAccount($model->company_id);
   
        try {

            $service = new Products($access_token);
            
            $service->setData([
                'codigo_producto' => $model->id, 
                'codigo_producto_sin' => $model->custom_value1,
                'codigo_unidad' => $model->custom_value2,
                'nombre_unidad' => $model->custom_value3
            ]);

            $service->homologate();
            
            $service->saveResponse();
            
            $resp = $service->getResponse();
            $model->custom_value4 = $resp['codigoActividadEconomica']."";
            $model->save();

            return true;

        } catch(ClientFelException $ex) {
            $model->custom_value1 = "";
            $model->custom_value2 = "";
            $model->custom_value3 = "";
            $model->custom_value4 = "";
            $model->save();
            // throw $ex;
        }
    }
}
