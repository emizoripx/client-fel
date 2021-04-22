<?php


namespace EmizorIpx\ClientFel\Traits;

use EmizorIpx\ClientFel\Models\FelClientToken;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

trait GetCredentialsTrait {

    public function getCredentials(){

        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $company_id_decode = $hashid->decode($this->company_id)[0];
        
        return FelClientToken::where('account_id', $company_id_decode)->firstOrFail();
    }

    public function setAccessToken(){
        Log::debug('Seteando credentials.....');
        $credentials = $this->getCredentials();

        $this->access_token = $credentials->getAccessToken();
        $this->host = $credentials->getHost();
        return $this;
    }

    
}