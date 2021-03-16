<?php


namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Models\FelClientToken;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

trait GetCredentialsTrait {

    public function getCredentials(){

        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $company_id_decode = $hashid->decode($this->company_id)[0];
        Log::debug('Obtenniendo los credenciales.....');
        return FelClientToken::where('account_id', $company_id_decode)->firstOrFail();
    }

    public function setAccessToken(){
        $this->access_token = $this->getCredentials()->getAccessToken();
        $this->host = $this->getCredentials()->getHost();
        return $this;
    }

    
}