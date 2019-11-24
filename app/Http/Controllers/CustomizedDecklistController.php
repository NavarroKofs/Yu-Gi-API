<?php

namespace App\Http\Controllers;

use App\customizedDecklist;
use Illuminate\Http\Request;

class CustomizedDecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $name = $request->data["name"];

      try {
          $cards = $request->data["cards"];
      } catch (\Exception $e) {
        $cards = [];
      }


      $answer = self::legality($cards);
      $legality = $answer[0];
      $cardTotal = $answer[1];
      $illegalCards = $answer[2];

      try {
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
      } catch (\Exception $e) {
        $response = [
                    "error"=> ["code"=> "423",
                               "description" => "Deck name already taken"
                              ]
                    ];
        return response()->json($response,423);
      }



        return response()->json($response,200);
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
      try {
          $cards = $request->data["cards"];
      } catch (\Exception $e) {
        $cards = [];
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
        $deckList = customizedDecklist::find($name);
        $cards = $deckList->cards;
        for ($i=0; $i < count($cards) ; $i++) {
          if($cards[$i]['name']==$request->data["card"]){
            unset($cards[$i]);
          }
        }
        var_dump($cards);
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
        $deckList = customizedDecklist::find($name);

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
     * Show the form for editing the specified resource.
     *
     * @param  \App\customizedDecklist  $customizedDecklist
     * @return \Illuminate\Http\Response
     */
    public function edit(customizedDecklist $customizedDecklist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\customizedDecklist  $customizedDecklist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, customizedDecklist $customizedDecklist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\customizedDecklist  $customizedDecklist
     * @return \Illuminate\Http\Response
     */
    public function destroy(customizedDecklist $customizedDecklist)
    {
        //
    }

    public function legality($cards){
      $illegalCards = array();
      $legality=true;
      $cardTotal=0;
      print("->".$cards[0]["amount"]);
      print("->".count($cards));


      for ($i=0; $i < count($cards) ; $i++) {
        $legalAmount;
        print("->".$cards[$i]["amount"]);
        $cardTotal+=$cards[0]["amount"];
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
