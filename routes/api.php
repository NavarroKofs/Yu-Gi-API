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

Route::post('v1/wishlist/create',"CustomizedWishListController@newWishlist");
Route::get('v1/wishlist/',"CustomizedWishListController@getWishList");
Route::get('v1/wishlist/{name}','CustomizedWishListController@findCard');
Route::get('v1/wishlist/tPrice/{name}','CustomizedWishListController@getTotalPrice');
Route::put('v1/wishlist/{name}','CustomizedWishListController@addCard');
Route::delete('v1/wishlist/','CustomizedWishListController@destroy');
Route::delete('v1/wishlist/{name}','CustomizedWishListController@removeCard');


