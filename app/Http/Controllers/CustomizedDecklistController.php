<?php

namespace App\Http\Controllers;

use App\customizedDecklist;
use Illuminate\Http\Request;

class CustomizedDecklistController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $name = $request->data["name"];

      if (customizedDecklist::find($name)) {
        $response = [
                    "error"=> ["code"=> "423",
                               "description" => "Deck name already taken"
                              ]
                    ];
        return response()->json($response,423);
      }


      try {
          $cards = $request->data["cards"];
          $answer = self::legality($cards);
          $legality = $answer[0];
          $cardTotal = $answer[1];
          $illegalCards = $answer[2];
      } catch (\Exception $e) {
        $cards = [];
        $legality = false;
        $cardTotal = 0;
        $illegalCards = [];
      }

        $deck = customizedDecklist::create([
                                            'name'=> $name,
                                            'cards'=> $cards,
                                            'legality'=> $legality,
                                            "illegalCards"=> $illegalCards,
                                            "size" => $cardTotal
                                          ]);
                $response = [
                            "data"=> ["name"=> $name,
                                      "cards"=> $cards,
                                      "size" => $cardTotal,
                                      "legality"=> $legality,
                                      "illegalCards"=> $illegalCards
                                      ]
                            ];

        return response()->json($response,201);
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

      if (!(customizedDecklist::find($name))) {
      return response()->json([
           "errors"=> ["ID"=> "ADD_CARD-1",
           "title"=>  "Decklist not found",
           "code"=>  "404",
           ]]  , 404);
         }

      try {
          $cards = $request->data["cards"];
      } catch (\Exception $e) {
        return response()->json([
             "errors"=> ["ID"=> "ADD_CARD-2",
             "title"=>  "Cards not found",
             "code"=>  "422",
             ]]  , 422);
      }

        $deckList = customizedDecklist::find($name);
        $newCards = $deckList->cards;
        for ($i=0; $i < count($cards) ; $i++) {
        array_push($newCards, $request->data["cards"][$i]);
      }

      $answer = self::legality($newCards);

      $deckList->cards = $newCards;
      $deckList->legality = $answer[0];
      $deckList->size = $answer[1];
      $deckList->illegalCards = $answer[2];
      $deckList->save();

      $response = [
                  "data"=> ["name"=> $name,
                            "cards"=> $deckList->cards,
                            "size" => $deckList->size,
                            "legality"=> $deckList->legality,
                            "illegalCards"=> $deckList->illegalCards
                            ]
                  ];


      return response()->json($response,200);
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
        if (!(customizedDecklist::find($name))) {
        return response()->json([
             "errors"=> ["ID"=> "REMOVE_CARD-1",
             "title"=>  "Decklist not found",
             "code"=>  "404",
             ]]  , 404);
           }

        $deckList = customizedDecklist::find($name);
        $cards = $deckList->cards;
        for ($i=0; $i < count($cards) ; $i++) {
          if($cards[$i]['name']==$request->data["card"]){
            array_splice($cards, $i, $i);
          }
        }
        $answer = self::legality($cards);

        $deckList->cards = $cards;
        $deckList->legality = $answer[0];
        $deckList->size = $answer[1];
        $deckList->illegalCards = $answer[2];
        $deckList->save();

      $response = [
                  "data"=> ["name"=> $name,
                            "cards"=> $deckList->cards,
                            "size" => $deckList->size,
                            "legality"=> $deckList->legality,
                            "illegalCards"=> $deckList->illegalCards
                            ]
                  ];


      return response()->json($response,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\customizedDecklist  $customizedDecklist
     * @return \Illuminate\Http\Response
     */
    public function viewDecklist($name)
    {
      if (!(customizedDecklist::find($name))) {
      return response()->json([
           "errors"=> ["ID"=> "ERR_SHOW-1",
           "title"=>  "Decklist not found",
           "code"=>  "404",
           ]]  , 404);
      } else {
        $deckList = customizedDecklist::findOrFail($name);

        $response = [
                    "data"=> ["name"=> $name,
                              "cards"=> $deckList->cards,
                              "size" => $deckList->size,
                              "legality"=> $deckList->legality,
                              "illegalCards"=> $deckList->illegalCards
                              ]
                    ];

        return response()->json($response,200);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\customizedDecklist  $customizedDecklist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $name = $request->data['name'];
        if (!(customizedDecklist::find($name))) {
        return response()->json([
             "errors"=> ["ID"=> "ERR_DELETE-1",
             "title"=>  "Decklist not found",
             "code"=>  "404",
             ]]  , 404);
        } else {
            customizedDecklist::destroy($name);
            return response()->json($request,204);
      }
    }

    public function legality($cards){
      $illegalCards = array();
      $legality=true;
      $cardTotal=0;


      for ($i=0; $i < count($cards) ; $i++) {

        $legalAmount;
        $cardTotal+=$cards[$i]["amount"];
        $cardName = str_replace(" ", "%20",$cards[$i]["name"]);
        $route = "https://db.ygoprodeck.com/api/v5/cardinfo.php?banlist=tcg&name=".$cardName;

        try {
          $banListJson = file_get_contents($route);
          $banListInfo = json_decode($banListJson,true);

          $status_line = $http_response_header[0];
          preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
          $status = $match[1];

            $cardStatus = $banListInfo[0]["banlist_info"]["ban_tcg"];
            switch ($cardStatus) {
              case 'Semi-Limited':
                $legalAmount=2;
                break;
              case 'Limited':
                $legalAmount=1;
                break;
              default:
                $legalAmount=0;
                break;
            }
        } catch (\Exception $e) {
          $legalAmount=3;
        }
        if ($cards[$i]["amount"]> $legalAmount) {
          $legality=false;
          array_push($illegalCards, $cards[$i]["name"]);
        }
      }
      if (($cardTotal<40)||($cardTotal>60)) {
        $legality=false;
      }

      $answer = [$legality, $cardTotal, $illegalCards];

        return $answer;
    }
}
