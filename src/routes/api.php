<?php


Route::group([ 'middleware' => ['api_db', 'token_auth', 'locale'], 'as' => 'api.', 'namespace' => "EmizorIpx\ClientFel\Http\Controllers" , "prefix" => "api/v1/clientfel/"] , function() {
    
    Route::post('registerCredentials', 'ConnectionController@registerCredentials');
    Route::get('getToken', 'ConnectionController@getToken');

});
