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
  'prefix' => 'contact'
  ], function($router) {
    Route::post('create', 'Api\Contacts\ContactsController@create');
    Route::post('{contact_id}/share', 'Api\Contacts\ContactsController@share');
    Route::delete('{contact_id}/remove', 'Api\Contacts\ContactsController@remove');
  });
