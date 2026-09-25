<?php

namespace EmizorIpx\ClientFel\routes;

use Illuminate\Support\Facades\Route;

class Doctors
{
    public static function routes()
    {
        Route::group(['middleware' => ['needs_access_token'], 'namespace' => "\EmizorIpx\ClientFel\Http\Controllers", 'prefix' => 'clientfel/doctors'], function () {
            Route::get('/', 'DoctorController@index');
            Route::post('/', 'DoctorController@store');
            Route::post('/import', 'DoctorController@import');
            Route::get('/{id}', 'DoctorController@show');
            Route::put('/{id}', 'DoctorController@update');
            Route::delete('/{id}', 'DoctorController@destroy');
            Route::patch('/{id}/toggle-status', 'DoctorController@toggleStatus');
            Route::get('/{id}/kardex', 'DoctorController@getKardex');
        });
    }
}
