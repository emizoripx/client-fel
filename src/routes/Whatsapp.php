<?php

namespace EmizorIpx\ClientFel\routes;

use Illuminate\Support\Facades\Route;

class Whatsapp
{

    public static function routes()
    {

        Route::group(["namespace" => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "whatsapp/"], function () {

            Route::post('callback', 'WhatsappController@callback');

        });
    }
}
