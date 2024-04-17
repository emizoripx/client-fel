<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['token_auth'],'namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "cobrosqr/"], function () {
    Route::post("createInvoice", "CobrosqrController@store");
    Route::post("unlink", "CobrosqrController@delete");
});


Route::group(['middleware' => ['token_auth'],'namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "cobrosqr/api/"], function () {
    Route::get("list-payments", "CobrosqrController@listPayments");
    Route::get("list-cash-closures", "CobrosqrController@listCashClosures");
    Route::get("check-qr", "CobrosqrController@checkQrId");
});


Route::group(['namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "cobrosqr/api/"], function () {
    Route::post("callback", "CobrosqrController@callbackPayment");
});