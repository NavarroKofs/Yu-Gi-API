<?php

namespace App\Http\Controllers;

use App\customizedDecklist;
use Illuminate\Http\Request;
use App\legality;

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
                    "error"=> ["code"=> "422",
                               "description" => "Deck name already taken"
                              ]
                    ];
        return response()->json($response,422);
      }


      try {
          $cards = $request->data["cards"];
          $answer = legality::legality($cards);
          $legality = $answer->legality;
          $cardTotal = $answer->cardsInDeck;
          $illegalCards = $answer->illegalCards;
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
    public function addCard(Request $request, $deckName)
    {

      if (!(customizedDecklist::find($deckName))) {
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

        $deckList = customizedDecklist::find($deckName);
        $newCards = $deckList->cards;
        for ($i=0; $i < count($cards) ; $i++) {
        array_push($newCards, $request->data["cards"][$i]);
      }

      $answer = legality::legality($newCards);

      $deckList->cards = $newCards;
      $deckList->legality = $answer->legality;
      $deckList->size = $answer->cardsInDeck;
      $deckList->illegalCards = $answer->illegalCards;
      $deckList->save();

      $response = [
                  "data"=> ["name"=> $deckName,
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
    public function removeCard(Request $request, $deckName, $cardName)
    {
        if (!(customizedDecklist::find($deckName))) {
        return response()->json([
             "errors"=> ["ID"=> "REMOVE_CARD-1",
             "title"=>  "Decklist not found",
             "code"=>  "404",
             ]]  , 404);
           }

        $deckList = customizedDecklist::find($deckName);
        $cards = $deckList->cards;
        for ($i=0; $i < count($cards) ; $i++) {
          if($cards[$i]['name']==$cardName){
            array_splice($cards, $i, $i);
          }
        }
        $answer = legality::legality($cards);

        $deckList->cards = $cards;
        $deckList->legality = $answer->legality;
        $deckList->size = $answer->cardsInDeck;
        $deckList->illegalCards = $answer->illegalCards;
        $deckList->save();

      $response = [
                  "data"=> ["name"=> $deckName,
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
    public function viewDecklist($deckName)
    {
      if (!(customizedDecklist::find($deckName))) {
      return response()->json([
           "errors"=> ["ID"=> "ERR_SHOW-1",
           "title"=>  "Decklist not found",
           "code"=>  "404",
           ]]  , 404);
      } else {
        $deckList = customizedDecklist::findOrFail($deckName);

        $response = [
                    "data"=> ["name"=> $deckName,
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
    public function destroy(Request $request, $deckName)
    {
        if (!(customizedDecklist::find($deckName))) {
        return response()->json([
             "errors"=> ["ID"=> "ERR_DELETE-1",
             "title"=>  "Decklist not found",
             "code"=>  "404",
             ]]  , 404);
        } else {
            customizedDecklist::destroy($deckName);
            return response()->json($request,204);
      }
    }
}
