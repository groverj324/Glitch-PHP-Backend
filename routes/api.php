<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth/register', 'App\Http\Controllers\AuthController@register');
Route::post('auth/login', 'App\Http\Controllers\AuthController@login');


Route::get('events', 'App\Http\Controllers\EventController@index');
Route::post('events', 'App\Http\Controllers\EventController@store');
Route::get('events/{uuid}', 'App\Http\Controllers\EventController@show');
Route::put('events/{uuid}', 'App\Http\Controllers\EventController@update');
Route::delete('events/{uuid}', 'App\Http\Controllers\EventController@destroy');

Route::get('messages', 'App\Http\Controllers\MessageController@getConversations');
Route::get('messages/threads', 'App\Http\Controllers\MessageController@getConversations');
Route::post('messages/makeThread', 'App\Http\Controllers\MessageController@conversations');
Route::post('messages', 'App\Http\Controllers\MessageController@store');
Route::put('messages/{uuid}', 'App\Http\Controllers\MessageController@update');
Route::delete('messages/{uuid}', 'App\Http\Controllers\MessageController@destroy');


Route::get('users', 'App\Http\Controllers\UserController@index');
Route::put('users', 'App\Http\Controllers\UserController@update');
Route::get('users/{uuid}/profile/', 'App\Http\Controllers\UserController@profile');
Route::get('users/me', 'App\Http\Controllers\UserController@me');
Route::get('users/oneTimeToken', 'App\Http\Controllers\UserController@onetimetoken');
Route::get('users/{uuid}/followers', 'App\Http\Controllers\UserController@followers');
Route::get('users/{uuid}/following', 'App\Http\Controllers\UserController@following');
Route::post('users/{uuid}/follow', 'App\Http\Controllers\UserController@toggleFollow');
