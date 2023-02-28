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
            Route::post('invoices/status', 'InvoiceController@getStatus');
            Route::post('invoices/reversion-revocate', 'InvoiceController@reversionRevocate');
            Route::get('verify_nit/{NIT}', 'InvoiceController@verifynit');

            Route::post("products/check-code", "ProductController@checkCode");
            Route::post("clients/check-code", "ClientController@checkCode");
            Route::prefix('reports')->group(function () {
                
                Route::get('/', 'FelReportController@index');
                Route::post('generate', 'FelReportController@getGenerateReport');

            });

            Route::prefix('offline')->group(function () {
                
                Route::get('invoice/{sectorDocument}/{branchCode}/{posCode}/get-cuis-cufd', 'FelCuisCufdController@getCuisCufd');

                Route::post('event/{event_id}/end-offline', 'FelOfflineEventController@processEvent');

                Route::post('event/{event_id}/close', 'FelOfflineEventController@closeEvent');

                Route::get('event/{event_id}/status', 'FelOfflineEventController@getFelStatusEvent');

            });

        });
    }
}
