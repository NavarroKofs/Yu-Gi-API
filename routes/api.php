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

Route::get('v1/cards/search', 'cardsController@fuzzySearch');

Route::get('v1/cards/{card_name}', 'cardsController@searchByName');

Route::get('v1/cards/', 'cardsController@showAllCards');

Route::get('v1/cards/banlist/{ruling_type}', 'cartasController@showBanlist');

Route::get('v1/cards/set/{set_name}', 'cartasController@showAllCardsOfASet');

Route::get('v1/cards/archetype/{archetype_name}', 'cartasController@showAllCardsOfAnArchetype');
