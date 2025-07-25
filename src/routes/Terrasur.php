<?php

use Illuminate\Support\Facades\Route;

Route::group(['midlleware'=>["token_auth"], 'namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "terrasur/"], function () {
    Route::get("receipt/{id}/pdf", "TerrasurController@getReceiptPdf");
    Route::post("list-payment-types/{payment_type}", "TerrasurController@indexPaymentType");
    Route::post("list-payments/{payment_type}", "TerrasurController@index"); 
    Route::post("list-receipts", "TerrasurController@indexReceipts"); 
    Route::post("search", "TerrasurController@search"); // search
    Route::post("conciliate/{id}", "TerrasurController@conciliate");
});
