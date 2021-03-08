<?php

namespace EmizorIpx\ClientFel\routes;

use Illuminate\Support\Facades\Route;

class WebhookInvoice
{

    public static function routes()
    {

        Route::group(['namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "webhook/"], function () {

            Route::post('status-invoice', 'WebhookController@callback');
        });
    }
}
