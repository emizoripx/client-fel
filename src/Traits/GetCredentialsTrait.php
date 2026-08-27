<?php

namespace EmizorIpx\ClientFel\Traits;

use App\Models\Company;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PartnerConfiguration;
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

        $hashid = new Hashids(config('ninja.hash_salt'), 10);
        $company_id_decode = $hashid->decode($this->company_id)[0];

        $company = Company::find($company_id_decode);

        if ($company && !empty($company->settings->is_b2b2b_partner)) {
            $accountPrepago = AccountPrepagoBags::where('company_id', $company_id_decode)->first();
            $phase = $accountPrepago->phase ?? 'Production';
            $partnerPhase = in_array(strtolower($phase), ['testing', 'piloto testing', 'piloto']) ? 'testing' : 'production';

            $config = PartnerConfiguration::getConfig($partnerPhase);

            $this->access_token = $config['partner_token'] ?? '';
            $this->host = $config['api_url'] ?? '';
            $this->tenant_key = $company->settings->tenant_id ?? $company->settings->id_number;

            Log::debug("GetCredentialsTrait: Empresa Partner detectada (ID: {$company_id_decode}). Seteando Master Token y tenant-key: {$this->tenant_key}");
            return $this;
        }

        $credentials = $this->getCredentials();

        $this->access_token = $credentials->getAccessToken();
        $this->host = $credentials->getHost();
        $this->tenant_key = null;
        return $this;
    }
}
