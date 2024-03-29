<?php

namespace EmizorIpx\ClientFel\Http\Middleware;

use Closure;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Models\FelClientToken;
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

            $client_token = FelClientToken::getTokenByAccount($companyId);

        } catch (ClientFelException $ex) {
            $error = [
                'message' => $ex->getMessage(),
                'errors' => new stdClass,
            ];

            return response()->json($error, 403);
        }

        $request_array['access_token'] = $client_token->getAccessToken();
        $request_array['host'] = $client_token->getHost();
        $request_array['company_id'] = $companyId;
        $request_array['company_name'] = $company->settings->name;
        $request->replace($request_array);

        return $next($request);
    }
}
