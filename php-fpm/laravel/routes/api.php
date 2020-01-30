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

require('Api/Auth/auth.php');
require('Api/Groups/groups.php');
require('Api/Users/users.php');
require('Api/Contacts/contacts.php');
