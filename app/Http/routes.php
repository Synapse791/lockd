<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group([
    'prefix' => '/api',
    'namespace' => 'Api',
    'middleware' => ['api'],
], function () {

    Route::group(['prefix' => '/user'], function () {
        Route::get('/', 'UserController@get');
        Route::put('/', 'UserController@create');
        Route::patch('/{id}', 'UserController@update');
    });

    Route::group(['prefix' => '/group'], function () {
        Route::get('/', 'GroupController@get');
        Route::put('/', 'GroupController@create');
        Route::patch('/{id}', 'GroupController@update');
    });

});