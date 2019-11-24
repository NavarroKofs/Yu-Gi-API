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

Route::get('v1/cartas/{nombre_carta}', 'cartasController@busqueda');

Route::get('v1/cartas/', 'cartasController@show_all_cards');
Route::post('v1/decklist/',"CustomizedDecklistController@store");
Route::delete('v1/decklist/',"CustomizedDecklistController@destroy");
Route::put('v1/decklist/{name}',"CustomizedDecklistController@addCard");
Route::delete('v1/decklist/{name}',"CustomizedDecklistController@removeCard");
Route::get('v1/decklist/{name}',"CustomizedDecklistController@viewDecklist");
