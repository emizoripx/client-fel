<?php

namespace EmizorIpx\ClientFel\routes;

use Illuminate\Support\Facades\Route;

class WebhookInvoice
{

    public static function routes()
    {

        Route::group(['namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "webhook/"], function () {

            Route::post('status-invoice', 'WebhookController@callback');
            
            Route::post('update-branch', 'WebhookBranch@updateBranch');
            
            Route::post('update-pos', 'WebhookPos@updatePos');
            
            Route::post('update-parametrics', 'WebhookParametrics@updateParametrics');
            
            Route::post('status-package', 'PackageWebhookController@callback');
            
            Route::post('update-templates', 'WebhookTemplate@updateTemplates');

            Route::post('update-documents', 'WebhookDocumentSector@updateDocumentSector');
        });
    }
}
