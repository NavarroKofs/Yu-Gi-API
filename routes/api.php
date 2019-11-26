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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('v1/usuariosy', 'YUserController@store');
Route::post('v1/login', 'Auth\LoginController@login');
Route::post('v1/logout', 'Auth\LoginController@logout');
Route::post('v1/resetPass', 'Auth\ResetPasswordController@resetPassword');
