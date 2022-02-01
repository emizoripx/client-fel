<?php

namespace EmizorIpx\ClientFel\Http\Middleware;

use Closure;
use EmizorIpx\ClientFel\Models\FelClient;
use Illuminate\Http\Request;
use stdClass;
use Hashids\Hashids;

class ValidateSpecialCodes
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

        $change_document = false;
        
        if( $request->has('type_document_id') && $request->has('id_number') ){

            if($request_array['id_number'] == 0){
                $change_document = true;
            }

        } else {
            $hashid = new Hashids(config('ninja.hash_salt'), 10);

            $id_decode = $hashid->decode($request_array['client_id'])[0];

            $client = FelClient::where('id_origin', $id_decode)->where('document_number', 0)->first();

            if( $client ){
                \Log::debug("si exite CLiente >>>>>> Document 0");

                $change_document = true;
            }

        }

        if($change_document){
            \Log::debug("Change data >>>>>>>>>>>>>>>>> Document Number");
            $request_array['type_document_id'] = 4 ;
            $request_array['name'] = 'CONTROL TRIBUTARIO' ;
            $request_array['id_number'] = "99002" ;
        }

        $request->replace($request_array);

        return $next($request);
    }
}
