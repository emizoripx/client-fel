<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "terrasur/"], function () {
    Route::get("receipt/{id}/pdf", "TerrasurController@getReceiptPdf");
    Route::get("list-payment-types/{payment_type}", "TerrasurController@indexPaymentType");
    Route::get("list-payments/{payment_type}", "TerrasurController@index"); 
    Route::get("search", "TerrasurController@search"); // search
});
