<?php


Route::group(['namespace' => "EmizorIpx\ClientFel\Http\Controllers" , "prefix" => "api/v1/clientfel/"] , function() {

    
    Route::post('registerCredentials', 'ConnectionController@registerCredentials');
    Route::get('getToken', 'ConnectionController@getToken');

    

});
