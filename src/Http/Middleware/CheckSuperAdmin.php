<?php

namespace EmizorIpx\ClientFel\Http\Middleware;

use App\Models\Company;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Hashids\Hashids;

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
        
        if (!Auth::check()) {
            
            if (!$request->has('user')) {
                return redirect()->to('/');
            }

            $hashids = new Hashids(config('ninja.hash_salt'), 10);
            $user_id = $hashids->decode($request->query('user'))[0];


            $user = User::where('id', $user_id)->first();

            if ($user && $user->is_superadmin) {

                Auth::login($user);

                return $next($request);
            }
        } else {
            if (Auth::user()->is_superadmin) {
                return $next($request);
            }
        }

        return redirect()->to('/');
    }
}
