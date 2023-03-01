<?php

use Illuminate\Support\Facades\Route;

Route::group([ 'namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix"=>"agencias/"], function () {
    Route::post("/verificar-poliza", "AgencyController@verifyPolicy");
    Route::get("/", "AgencyController@index");
    Route::get("/{id}", "AgencyController@show");
    Route::put("/{id}", "AgencyController@update");
    Route::post("/", "AgencyController@store");
});
