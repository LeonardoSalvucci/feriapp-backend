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
  'prefix' => 'group'
  ], function($router) {
    Route::post('create', 'Api\Groups\GroupsController@create');
    Route::delete('{group_id}/remove', 'Api\Groups\GroupsController@remove');
    Route::post('{group_id}/addUser', 'Api\Groups\GroupsController@addUser');
    Route::post('{group_id}/removeUser', 'Api\Groups\GroupsController@removeUser');
    Route::post('{group_id}/addContact', 'Api\Groups\GroupsController@addContact');
    Route::delete('{group_id}/removeContact', 'Api\Groups\GroupsController@removeContact');

  });
