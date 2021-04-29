<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => "\EmizorIpx\ClientFel\Http\Controllers"], function () {
    Route::get("bitacora", "BitacoraController@index");
    Route::get("updateTokens", "BitacoraController@updateTokens");
});