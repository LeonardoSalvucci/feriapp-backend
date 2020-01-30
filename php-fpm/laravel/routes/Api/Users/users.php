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
  'middleware' => ['auth:api'],
  'prefix' => 'user'
  ], function($router) {
    Route::get('getMyGroups', 'Api\Users\UsersController@getMyGroups');
    Route::get('getMyProfile', 'Api\Users\UsersController@getMyProfile');
    Route::get('{user_id}/getGroups', 'Api\Users\UsersController@getGroups');
    Route::get('{user_id}/getProfile', 'Api\Users\UsersController@getProfile');

  });