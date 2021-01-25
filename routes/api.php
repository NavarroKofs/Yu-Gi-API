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
Auth::routes();

//wishlist
Route::post('v1/wishlist/create',"customizedWishListController@newWishlist")->middleware('auth');

Route::get('v1/wishlist/{id}',"customizedWishListController@getWishList")->middleware('auth');

Route::get('v1/wishlist/tPrice/{id}','customizedWishListController@getTotalPrice')->middleware('auth');

Route::get('v1/wishlist/fCard/{id}/{name}','customizedWishListController@findCard')->middleware('auth');

Route::put('v1/wishlist/{id}','customizedWishListController@addCard')->middleware('auth');

Route::delete('v1/wishlist/{id}','customizedWishListController@destroy')->middleware('auth');

Route::delete('v1/wishlist/rCard/{id}/{name}','customizedWishListController@removeCard')->middleware('auth');


//Search

Route::get('v1/cards/search', 'cardsController@fuzzySearch')->middleware('auth');

Route::get('v1/cards/{card_name}', 'cardsController@searchByName')->middleware('auth');

Route::get('v1/cards/', 'cardsController@showAllCards')->middleware('auth');

Route::get('v1/cards/banlist/{ruling_type}', 'cardsController@showBanlist')->middleware('auth');

Route::get('v1/cards/set/{set_name}', 'cardsController@showAllCardsOfASet')->middleware('auth');

Route::get('v1/cards/archetype/{archetype_name}', 'cardsController@showAllCardsOfAnArchetype')->middleware('auth');

//decklist

Route::post('v1/decklist/',"CustomizedDecklistController@store")->middleware('auth');

Route::delete('v1/decklist/{deckName}',"CustomizedDecklistController@destroy")->middleware('auth');

Route::put('v1/decklist/{deckName}/',"CustomizedDecklistController@addCard")->middleware('auth');

Route::delete('v1/decklist/{deckName}/{cardName}',"CustomizedDecklistController@removeCard")->middleware('auth');

Route::get('v1/decklist/{name}',"CustomizedDecklistController@viewDecklist")->middleware('auth');

//Deckpersonalizado

Route::post('v1/customizedCard', "customizedCardsController@store")->middleware('auth');

Route::get('v1/customizedCard', "customizedCardsController@showCards")->middleware('auth');

Route::delete('v1/customizedCard', "customizedCardsController@removeCard")->middleware('auth');

Route::put('v1/customizedCard', "customizedCardsController@updateCard")->middleware('auth');

//user

Route::post('v1/user', 'UserController@store');

Route::post('v1/login', 'Auth\LoginController@login');

Route::post('v1/logout', 'Auth\LoginController@logout');

Route::post('v1/sendResetPass', 'Auth\ResetPasswordController@resetPassword');

Route::post('v1/resetPass1', 'Auth\ResetPasswordController@resetPasswordComplete');
