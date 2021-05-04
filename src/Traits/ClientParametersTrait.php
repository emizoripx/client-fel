<?php 

namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Models\FelClient;

trait ClientParametersTrait {


    public function fel_client(){
        return $this->hasOne(FelClient::class, 'id_origin', 'id')->withTrashed();
    }

    public function includeFelData(){
        $client = $this->fel_client;

        return is_null($client) ? null : $client;
    }
}