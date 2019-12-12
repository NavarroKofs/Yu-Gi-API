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


//wishlist
Route::post('v1/wishlist/create',"customizedWishListController@newWishlist");
Route::get('v1/wishlist/{id}',"CustomizedWishListController@getWishList");
Route::get('v1/wishlist/tPrice/{id}','CustomizedWishListController@getTotalPrice');
Route::get('v1/wishlist/fCard/{id}/{name}','CustomizedWishListController@findCard');
Route::put('v1/wishlist/{id}','CustomizedWishListController@addCard');
Route::delete('v1/wishlist/{id}','CustomizedWishListController@destroy');
Route::delete('v1/wishlist/rCard/{id}/{name}','CustomizedWishListController@removeCard');


//Search

Route::get('v1/cards/search', 'cardsController@fuzzySearch');

Route::get('v1/cards/{card_name}', 'cardsController@searchByName');

Route::get('v1/cards/', 'cardsController@showAllCards');

Route::get('v1/cards/banlist/{ruling_type}', 'cardsController@showBanlist');

Route::get('v1/cards/set/{set_name}', 'cardsController@showAllCardsOfASet');

Route::get('v1/cards/archetype/{archetype_name}', 'cardsController@showAllCardsOfAnArchetype');

//decklist

Route::post('v1/decklist/',"CustomizedDecklistController@store");

Route::delete('v1/decklist/{deckName}',"CustomizedDecklistController@destroy");

Route::put('v1/decklist/{deckName}/',"CustomizedDecklistController@addCard");

Route::delete('v1/decklist/{deckName}/{cardName}',"CustomizedDecklistController@removeCard");

Route::get('v1/decklist/{name}',"CustomizedDecklistController@viewDecklist");

//Deckpersonalizado

Route::post('v1/user', 'UserController@store');

Route::post('v1/login', 'Auth\LoginController@login');

Route::post('v1/logout', 'Auth\LoginController@logout');

Route::post('v1/sendResetPass', 'Auth\ResetPasswordController@resetPassword');

Route::post('v1/resetPass1', 'Auth\ResetPasswordController@resetPasswordComplete');
