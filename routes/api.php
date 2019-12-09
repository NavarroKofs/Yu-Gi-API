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

Route::post('v1/wishlist/create',"customizedWishListController@newWishlist");
Route::get('v1/wishlist/{id}',"CustomizedWishListController@getWishList");
Route::get('v1/wishlist/tPrice/{id}','CustomizedWishListController@getTotalPrice');
Route::get('v1/wishlist/fCard/{id}/{name}','CustomizedWishListController@findCard');
Route::put('v1/wishlist/{id}','CustomizedWishListController@addCard');
Route::delete('v1/wishlist/{id}','CustomizedWishListController@destroy');
Route::delete('v1/wishlist/rCard/{id}/{name}','CustomizedWishListController@removeCard');


