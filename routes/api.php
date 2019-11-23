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

Route::post('decklist',"CustomizedDecklistController@store");

Route::get('v1/cartas/banlist/{ruling_type}', 'cartasController@show_banlist');

Route::get('v1/cartas/set/{set_name}', 'cartasController@show_all_cards_of_a_set');

Route::get('v1/cartas/archetype/{archetype_name}', 'cartasController@show_all_cards_of_archetype');
