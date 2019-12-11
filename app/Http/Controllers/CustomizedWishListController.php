<?php
namespace App\Http\Controllers;
use App\customizedWishList;
use App\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
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
    $cards = $request->data["cards"];
    $totalPrice = self::calculatePrice($cards);
  
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
  public function getWishList(Request $request, $id)
  {
    if (!(customizedWishList::find($id))) {
      return response()->json([
           "errors"=> ["ID"=> "ERR_SHOW-1",
           "title"=>  "WishList not found",
           "code"=>  "404",
           ]]  , 404);
      } 
      
      $wishlistDB = DB::table('customized_wish_lists')->where('id', '=', $id)->first();
	    $data = collect($wishlistDB);
	  
	  $page = request()->has('page') ? request('page') : 1;
	  // Set default per page
	  $perPage = request()->has('per_page') ? request('per_page') : 15;
	  // Offset required to take the results
	  $offset = ($page * $perPage) - $perPage;
	  
      $response = self::pagination($data, $offset, $page, $perPage);
      return response()->json($response,200);
  }
  /**
   * get the wishlist total price.
   *
   * @param  Request  $request
   * @param  string $name
   * @return Response
  */
  public function getTotalPrice(Request $request, $id){
    if (!(customizedWishList::find($id))) {
      return response()->json([
           "errors"=> ["ID"=> "ERR_SHOW-1",
           "title"=>  "WishList not found",
           "code"=>  "404",
           ]]  , 404);
    } 
      
    $wishlistDB = DB::table('customized_wish_lists')->select('price')->where('id', '=', $id)->first();
    return response()->json($wishlistDB,200);
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\customizedWishList  $customizedWishlist
   * @return \Illuminate\Http\Response
   */
    public function destroy(Request $request, $id)
    {
        if (!(customizedWishList::find($id))) {
          return response()->json([
              "errors"=> ["ID"=> "ERR_DELETE-1",
              "title"=>  "Wishlist not found",
              "code"=>  "404",
              ]]  , 404);
        } else {
            customizedWishList::destroy($id);
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
    public function removeCard(Request $request, $id, $name)
    {
        if (!(customizedWishList::find($id))) {
        return response()->json([
             "errors"=> ["ID"=> "REMOVE_CARD-1",
             "title"=>  "Wishlist not found",
             "code"=>  "404",
             ]]  , 404);
           }
        $WishList = customizedWishList::find($id);
        $cards = $WishList->cards;
        for ($i=0; $i < count($cards) ; $i++) {
          if($cards[$i]==$name){
            array_splice($cards, $i, $i);
          }
        }
        $newPrice = self::calculatePrice($cards);
        $WishList->cards = $cards;
        $WishList->price = $newPrice;
        $WishList->save();
        
        return response()->json($WishList,200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $name
     * @return \Illuminate\Http\Response
     */
    public function addCard(Request $request, $id)
    {
        if (!(customizedWishList::find($id))) {
        return response()->json([
             "errors"=> ["ID"=> "REMOVE_CARD-1",
             "title"=>  "Wishlist not found",
             "code"=>  "404",
             ]]  , 404);
           }
        $WishList = customizedWishList::find($id);
        $cards = $WishList->cards;
        $addCards = $request->cards;
        
        for ($i=0; $i < count($addCards) ; $i++) {
          array_push($cards,$addCards[$i]);
        }
        $newPrice = self::calculatePrice($cards);
        $WishList->cards = $cards;
        $WishList->price = $newPrice;
        $WishList->save();
      
        return response()->json($WishList,200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $name
     * @return \Illuminate\Http\Response
     */
    public function findCard(Request $request, $id, $name)
    {
        if (!(customizedWishList::find($id))) {
        return response()->json([
             "errors"=> ["ID"=> "REMOVE_CARD-1",
             "title"=>  "Wishlist not found",
             "code"=>  "404",
             ]]  , 404);
           }

        $name = strtolower ($name);
        $cardFound=null;
        $WishList = customizedWishList::find($id);
        $cards = $WishList->cards;
        for ($i=0; $i < count($cards) ; $i++) {
          if(strtolower ($cards[$i])==$name){
            $cardFound=$cards[$i];
          }
        }
        
        if (is_null($cardFound)) {
          return response()->json([
               "errors"=> ["ID"=> "REMOVE_CARD-1",
               "title"=>  "Card not found",
               "code"=>  "404",
               ]]  , 404);
             }
        $path = "https://db.ygoprodeck.com/api/v5/cardinfo.php?&fname=$cardFound";
        $cardResponse = self::getContent($path);
        $response = json_decode($cardResponse->getBody());
        return $response;
    }
	
	public function pagination($newCollection, $offset, $page ,$perPage){
		// Set custom pagination to result set
		$results =  new LengthAwarePaginator(
			$newCollection->slice($offset, $perPage),
			$newCollection->count(),
			$perPage,
			$page,
			['path' => request()->url(), 'query' => request()->query()]
		);
		return $results;
    }

  public function getContent($cardPath){
    try{
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $cardPath);
        return $response;
    }catch(\GuzzleHttp\Exception\RequestException $e){
        return $e->getResponse();
    }
  }

  public function calculatePrice($cards){
    $totalPrice = 0;
    for ($i=0; $i < count($cards) ; $i++) {
      $cardName = $cards[$i];
      $path = "https://db.ygoprodeck.com/api/v5/cardinfo.php?fname=".$cardName;
      $getPrice = self::getContent($path);
      $currentPrice = json_decode($getPrice->getBody(),true);
      $jsonPrice = $currentPrice['0']['card_prices']['amazon_price'];
      $totalPrice += $jsonPrice;
    }
    $dollar = DB::table('currencies')->whereId(1)->first()->valor;
	  $convertedPrice = ($dollar * $totalPrice);
    return $convertedPrice;
  }
}