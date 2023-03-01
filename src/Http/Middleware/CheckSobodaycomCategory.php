<?php

namespace EmizorIpx\ClientFel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSobodaycomCategory
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
        $category = $request->route('category');
        if (!in_array($category,['evento_rubro', 'grupos_artistas','medio_transmision'])) {
            return response()->json([
                "success" => false,
                "msg" => "ruta no encontrada"
            ], 400);
        }
        $request_array = $request->all();
        $request_array['category'] = $category;
        $request->replace($request_array);
        return $next($request);
    }
}
