<?php
namespace EmizorIpx\ClientFel\routes;

use Illuminate\Support\Facades\Route;

class Parametrics {

    public static function routes() {

        
        Route::group([ 'middleware' => ['needs_access_token'], 'namespace' => "\EmizorIpx\ClientFel\Http\Controllers" , "prefix" => "clientfel/"] , function() {
            
            Route::get('parametricas/{unidades}', 'ParametricController@index');
        });
    }

}