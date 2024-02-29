<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['token_auth'],'namespace' => "\EmizorIpx\ClientFel\Http\Controllers", "prefix" => "cobrosqr/"], function () {
    Route::post("createInvoice", "CobrosqrController@store");
    Route::post("unlink", "CobrosqrController@delete");
});
