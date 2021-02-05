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
        $access_token = null;
        try {
            
            $companyId = auth()->user()->company()->id;

            $access_token = FelClientToken::getTokenByAccount($companyId);

        } catch (ClientFelException $ex) {
            $error = [
                'message' => $ex->getMessage(),
                'errors' => new stdClass,
            ];

            return response()->json($error, 403);
        }

        $request_array['access_token'] = $access_token;
        $request_array['company_id'] = $companyId;
        $request->replace($request_array);

        return $next($request);
    }
}
