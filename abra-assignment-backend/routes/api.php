<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//User Route
Route::post('/register', 'App\Http\Controllers\UserController@userRegister');
Route::post('/login', 'App\Http\Controllers\UserController@userLogin');
Route::get('/logout', 'App\Http\Controllers\UserController@userLogout');

//Message Route
Route::post('/sendmessage', 'App\Http\Controllers\MessageController@sendMessage');
Route::get('/getallmessage', 'App\Http\Controllers\MessageController@getAllMessage');
Route::get('/getallunreadmessage', 'App\Http\Controllers\MessageController@getAllUnreadMessage');
Route::get('/readmessage/{messageId}', 'App\Http\Controllers\MessageController@readMessage');
Route::delete('/deletemessage/{messageId}', 'App\Http\Controllers\MessageController@deleteMessage');
