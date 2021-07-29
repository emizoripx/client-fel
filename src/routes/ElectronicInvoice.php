<?php

namespace EmizorIpx\ClientFel\routes;

use Illuminate\Support\Facades\Route;

class ElectronicInvoice
{

    public static function routes()
    {

        Route::group(['middleware' => ['needs_access_token'], 'namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "clientfel/"], function () {

            Route::post('invoices', 'InvoiceController@emit');
            Route::put('invoices', 'InvoiceController@updateEmitedInvoice');
            Route::post('invoices/revocate', 'InvoiceController@revocate');
            Route::post('invoices/reversion-revocate', 'InvoiceController@reversionRevocate');
        });
    }
}
