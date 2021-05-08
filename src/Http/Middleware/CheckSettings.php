<?php

namespace EmizorIpx\ClientFel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;

class CheckSettings
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
        $company = DB::table('companies')->where('company_key', $request->header('X-API-COMPANY-KEY'))->first();

        $clientfel = AccountPrepagoBags::where('company_id', $company->id)->first();
        
        \Log::debug("settings.....");
        
        if(!$clientfel){
            \Log::debug("clientfel null......");
    
            return response()->json([
                "success" => false,
                "msg" => "credentials not found"
            ]);

        }
        
        if(is_null($clientfel['settings'])){
            
            \Log::debug("settings null");

            return response()->json([
                "success" => false,
                "msg" => "settings parametric not found"
            ]);
        }

        return $next($request);
    }
}
