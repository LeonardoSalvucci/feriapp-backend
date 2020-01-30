<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
  'prefix' => 'auth'
], function($router) {
  Route::post('/login', 'Api\Auth\AuthController@login');
  Route::post('/register', 'Api\Auth\AuthController@register');
});

Route::group([
  'middleware' => ['auth:api'],
  'prefix' => 'auth'
  ], function($router) {
    Route::post('logout', 'Api\Auth\AuthController@logout');
    Route::get('me', 'Api\Auth\AuthController@me');
    Route::post('refresh', 'Api\Auth\AuthController@refresh');
  });
