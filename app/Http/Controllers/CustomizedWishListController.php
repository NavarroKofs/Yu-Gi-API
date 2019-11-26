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
  public function newWishlist(Request $request)
  {
    $name = $request->data['name'];
    if (customizedWishList::findOrFail($name)) {
      $response = [
                  "error"=> ["code"=> "423",
                             "description" => "Wish name already taken"
                            ]
                  ];
      return response()->json($response,423);
    }

    try {
      $cards = $request->data["cards"];
      $totalPrice = 0;

      for ($i=0; $i < count($cards) ; $i++) {
        $cardName = str_replace(" ", "%20",$cards[$i]["name"]);
        $getPrice = file_get_contents("https://db.ygoproWish.com/api/v5/cardinfo.php?fname="+$cardName);
        $currentPrice = json_decode($getPrice,true);
        $totalPrice += $currentPrice[0]['card_prices']['amazon_price'];
      }
    } catch (\Exception $e) {
      $cards = [];
      $totalPrice = 0;
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
  public function getWishList(Request $request)
  {
    $name = $request->data['name'];
    if (!(customizedWishList::find($name))) {
      return response()->json([
           "errors"=> ["ID"=> "ERR_SHOW-1",
           "title"=>  "WishList not found",
           "code"=>  "404",
           ]]  , 404);
      } 
      
      $wishlistDB = DB::table('wish_lists')->where('name', '=', $name)->first();
      $wishlistJson = json_encode($wishlistDB);
      $response = cartasController::paginacion($wishlistJson);

      return response()->json($response,200);
  }

  /**
   * get the wishlist total price.
   *
   * @param  Request  $request
   * @param  string $name
   * @return Response
  */
  public function getTotalPrice(Request $request){
    $name = $request->data['name'];
    if (!(customizedWishList::find($name))) {
      return response()->json([
           "errors"=> ["ID"=> "ERR_SHOW-1",
           "title"=>  "WishList not found",
           "code"=>  "404",
           ]]  , 404);
    } 
      
    $wishlistDB = DB::table('wish_lists')->select('price')->where('name', '=', $name)->first();
    $wishlistJson = json_encode($wishlistDB);

    return response()->json($wishlistJson,200);

  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\customizedWishList  $customizedWishlist
   * @return \Illuminate\Http\Response
   */

    public function destroy(Request $request)
    {
        $name = $request->data['name'];
        if (!(customizedWishList::find($name))) {
        return response()->json([
             "errors"=> ["ID"=> "ERR_DELETE-1",
             "title"=>  "Wishlist not found",
             "code"=>  "404",
             ]]  , 404);
        } else {
            customizedWishList::destroy($name);
            return response()->json($request,204);
      }
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $name
     * @return \Illuminate\Http\Response
     */
    public function removeCard(Request $request, $name)
    {
        if (!(customizedWishList::find($name))) {
        return response()->json([
             "errors"=> ["ID"=> "REMOVE_CARD-1",
             "title"=>  "Wishlist not found",
             "code"=>  "404",
             ]]  , 404);
           }
        $WishList = customizedWishList::find($name);
        $cards = $WishList->cards;
        for ($i=0; $i < count($cards) ; $i++) {
          if($cards[$i]['name']==$request->data["card"]){
            array_splice($cards, $i, $i);
          }
        }
        $WishList->cards = $cards;
        $WishList->save();
        
        $wishlistJson = json_encode($WishList);

        return response()->json($wishlistJson,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $name
     * @return \Illuminate\Http\Response
     */
    public function addCard(Request $request, $name)
    {
        if (!(customizedWishList::find($name))) {
        return response()->json([
             "errors"=> ["ID"=> "REMOVE_CARD-1",
             "title"=>  "Wishlist not found",
             "code"=>  "404",
             ]]  , 404);
           }
        $WishList = customizedWishList::find($name);

        $cards = $WishList->cards;
        $addCards = $request->cards;
        
        for ($i=0; $i < count($addCards) ; $i++) {
          array_push($cards,$addCards[$i]);
        }
        
        $WishList->cards = $cards;
        $WishList->save();
        
        $wishlistJson = json_encode($WishList);

        return response()->json($wishlistJson,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $name
     * @return \Illuminate\Http\Response
     */
    public function findCard(Request $request, $name)
    {
        if (!(customizedWishList::find($name))) {
        return response()->json([
             "errors"=> ["ID"=> "REMOVE_CARD-1",
             "title"=>  "Wishlist not found",
             "code"=>  "404",
             ]]  , 404);
           }

        $cardFound;
        $WishList = customizedWishList::find($name);
        $cards = $WishList->cards;
        for ($i=0; $i < count($cards) ; $i++) {
          if($cards[$i]['name']==$request->data["card"]){
            $cardFound=$cards[$i]['name'];
          }
        }
        
        if (is_null($cardFound)) {
          return response()->json([
               "errors"=> ["ID"=> "REMOVE_CARD-1",
               "title"=>  "Card not found",
               "code"=>  "404",
               ]]  , 404);
             }

        $response = "https://db.ygoprodeck.com/api/v5/cardinfo.php?&name=$cardFound";
        return response()->json($response,200);
    }

}
