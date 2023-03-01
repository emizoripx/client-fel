<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "sobodaycom/"], function () {
    Route::get("/autorizacion/{id}", "SobodaycomController@getAutorizacion");
    Route::group(['middleware' => ['check_sobodaycom_category']], function () {
            Route::get("/{category}", "SobodaycomController@index");
            Route::put("/{category}/{id}", "SobodaycomController@update");
            Route::post("/{category}", "SobodaycomController@store");
            Route::delete("/{category}/{id}", "SobodaycomController@delete");

    });
});
