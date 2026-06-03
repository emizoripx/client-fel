<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => "\EmizorIpx\ClientFel\Http\Controllers"], function () {
    Route::get("bitacora", "BitacoraController@index");
    Route::get("updateTokens", "BitacoraController@updateTokens");
    Route::get("getHtmlFromInvoice/{company_id}/{generate_pdf}/{use_thermal_printer}", "BitacoraController@getHtmlFromInvoice");
});