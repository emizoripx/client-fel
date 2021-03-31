<?php

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Models\FelSyncProduct;

trait ProductParametersTrait{


    public function fel_product(){
        return $this->hasOne(FelSyncProduct::class, 'id_origin')->withTrashed();
    }

    public function includeFelData(){
        $product = $this->fel_product;

        return is_null($product) ? null : $product;
    }
}