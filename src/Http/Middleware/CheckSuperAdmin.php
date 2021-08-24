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

        \Log::debug("Validation SUperadmin");
        if (!Auth::check()) {

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


        \Log::debug("User Not is Superadmin");

        return redirect()->to('/');
    }
}
