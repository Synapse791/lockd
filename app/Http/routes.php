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

Route::get('/install', function () {
    if (Storage::exists('setup.lock'))
        return Redirect::to('/');
    return View::make('install');
});

Route::group(['middleware' => ['setup-check']], function () {
    Route::get('/', function () {return View::make('login');});

    Route::group(['namespace' => 'Auth'], function() {
        Route::post('/login', 'AuthController@postLogin');
        Route::get('/logout', 'AuthController@getLogout');
    });

    Route::get('/dashboard', function () {return View::make('dashboard');})
        ->middleware(['auth']);
});

Route::group(['prefix' => '/api/install', 'namespace' => 'Api\Install'], function () {
    Route::get('/check/{check}', 'CheckController@check');
    Route::get('/database/{task}', 'DatabaseController@task');
    Route::post('/database/{task}', 'DatabaseController@task');
    Route::put('/administrator', 'AdministratorController@create');
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

    Route::group(['prefix' => '/folder'], function () {

        Route::get('/{id}/{option?}', 'FolderController@get')
            ->where([
                'id' => '^[0-9]+$',
                'option' => '^(folders|parent)$',
            ]);

        Route::put('/', 'FolderController@create');

        Route::patch('/{id}', 'FolderController@update');

        Route::group(['prefix' => '/{id}/passwords'], function() {

            Route::get('/', 'PasswordController@getFromFolder');
            Route::put('/', 'PasswordController@create');
            Route::patch('/{passwordId}', 'PasswordController@update');

        });

    });

});