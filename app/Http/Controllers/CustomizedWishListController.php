<?php

namespace App\Http\Controllers;

use App\customizedWishList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class customizedWishListController extends Controller
{
  /**
   * create a wishlist.
   *
   * @param  Request  $request
   * @param  array  $wishlist
   * @return Response
  */
  public function newWishlist(Request $request, $wishlist)
  {
    $name = $wishlist->data["name"];
    $cards = $wishlist->data["cards"];
    $totalPrice = 0;
    for ($i=0; $i < count($cards) ; $i++) {
      $cardName = str_replace(" ", "%20",$cards[$i]["name"]);
      $getPrice = file_get_contents("https://db.ygoprodeck.com/api/v5/cardinfo.php?fname="+$cardName);
      $currentPrice = json_decode($getPrice,true);
      $totalPrice += $currentPrice[0]['card_prices']['amazon_price'];
    }

    $wishlist = customizedWishList::create([
                                        'name'=> $name,
                                        'cards'=> $cards,
                                        'price'=> $totalPrice
                                      ]);
    $response = [
                "data"=> ["name"=> $name,
                          "cards"=> $cards,
                          "price"=> $totalPrice
                          ]
                ];

    return response()->json($response,201);
  }

  /**
   * get the wishlist.
   *
   * @param  Request  $request
   * @param  string $name
   * @return Response
  */
  public function getWishList(Request $request, $name)
  {
    $wishlist = DB::select;



  }
}
