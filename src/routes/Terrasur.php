<?php

namespace EmizorIpx\ClientFel\routes;

use Illuminate\Support\Facades\Route;

class Terrasur {

    public static function routes() {

        Route::group(['middleware'=>["needs_access_token"], 'namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "terrasur/"], function () {
            Route::get("receipt/{id}/pdf", "TerrasurController@getReceiptPdf");
            Route::post("list-payment-types/{payment_type}", "TerrasurController@indexPaymentType");
            Route::post("list-payments/{payment_type}", "TerrasurController@index"); 
            Route::post("list-receipts", "TerrasurController@indexReceipts"); 
            Route::post("search", "TerrasurController@search"); // search
            Route::post("conciliate/{id}", "TerrasurController@conciliate");
            Route::get("branches/{code}", "TerrasurController@branchInfo");
        });
    }

}