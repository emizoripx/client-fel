<?php

namespace EmizorIpx\ClientFel\routes;

use Illuminate\Support\Facades\Route;

class Orders {

    public static function routes () {

        Route::group(['middleware' => ['check_settings'], 'namespace' => '\EmizorIpx\ClientFel\Http\Controllers', 'prefix' => 'shop'], function (){

            Route::post('orders', 'OrderController@store');
            
        });

    }

}