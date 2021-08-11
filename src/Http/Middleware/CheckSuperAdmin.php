<?php

namespace EmizorIpx\ClientFel\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckSuperAdmin
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
        $company = Company::where('company_key', $request->header('X-API-COMPANY-KEY'))->first();

        if($company && $company->owner()->is_superadmin){

            $request_array = $request->all();

            $request_array['company'] = $company;
            $request->replace($request_array);

            return $next($request);
        }
        \Log::debug("User Not is Superadmin");

        return redirect()->to('/');
    
    }
}
