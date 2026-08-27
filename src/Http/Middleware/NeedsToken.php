<?php

namespace EmizorIpx\ClientFel\Http\Middleware;

use Closure;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PartnerConfiguration;
use Illuminate\Http\Request;
use stdClass;

class NeedsToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $request_array = $request->all();
        $client_token = null;
        try {
                
             if ($request->header('X-API-COMPANY-KEY')) {

                $company = app(config('clientfel.entity_table_company'));
                $company = $company::where('company_key', request()->header('X-API-COMPANY-KEY'))->firstOrFail();
                $companyId = $company->id;

            } else {
                $company = auth()->user()->company();
                $companyId = auth()->user()->company()->id;
            }

            if (!empty($company->settings->is_b2b2b_partner)) {
                $accountPrepago = AccountPrepagoBags::where('company_id', $companyId)->first();
                $phase = $accountPrepago->phase ?? 'Production';
                $partnerPhase = in_array(strtolower($phase), ['testing', 'piloto testing', 'piloto']) ? 'testing' : 'production';
                $partnerConfig = PartnerConfiguration::getConfig($partnerPhase);

                $accessToken = $partnerConfig['partner_token'] ?? null;
                $host = $partnerConfig['api_url'] ?? null;
                $tenantKey = $company->settings->tenant_id ?? ($company->settings->id_number ?? $company->id);

                if (empty($accessToken)) {
                    throw new ClientFelException('No tiene registrado un access token');
                }

                $request_array['access_token'] = $accessToken;
                $request_array['host'] = $host;
                $request_array['tenant_key'] = $tenantKey;
            } else {
                $client_token = FelClientToken::getTokenByAccount($companyId);
                $request_array['access_token'] = $client_token->getAccessToken();
                $request_array['host'] = $client_token->getHost();
            }

        } catch (ClientFelException $ex) {
            $error = [
                'message' => $ex->getMessage(),
                'errors' => new stdClass,
            ];

            return response()->json($error, 403);
        }

        $request_array['company_id'] = $companyId;
        $request_array['company_name'] = $company->settings->name;
        $request->replace($request_array);

        return $next($request);
    }
}

