<?php
namespace EmizorIpx\ClientFel\routes;

use Illuminate\Support\Facades\Route;

class Credentials {

    public static function routes() {

        
        Route::group([  'namespace' => "\EmizorIpx\ClientFel\Http\Controllers" , "prefix" => "clientfel/"] , function() {
            
            Route::post('registerCredentials', 'ConnectionController@registerCredentials');
            Route::get('getToken', 'ConnectionController@getToken');
            
            Route::group(['middleware' => ['needs_access_token']] , function() {
                Route::post('homologateProduct', 'ProductController@homologate');
                Route::post('settings', 'ConnectionController@updateSettings');
            });
            
        });
    }

}