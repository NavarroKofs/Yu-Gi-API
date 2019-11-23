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
      $cards = $request->data["cards"];
      $cardTotal=0;
      $legality=true;
      for ($i=0; $i < count($cards) ; $i++) {
        $legalAmount;
        $cardTotal+=$cards[$i]["amount"];
        $cardName = str_replace(" ", "%20",$cards[$i]["name"]);
        $banListJson = file_get_contents("https://db.ygoprodeck.com/api/v5/cardinfo.php?banlist=tcg&name="+$cardName);
        $banListInfo = json_decode($banListJson,true);

        $status_line = $http_response_header[0];
        preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
        $status = $match[1];

        if($status == 400){
          $legalAmount = 3;
        }else{
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
        }
        if ($cards[$i]["amount"]> <Aqui va el límite de copias permitidas para cada carta>) {
          $limitedLegality=false;
        }
        }
        if (($cardTotal<40)||($cardTotal>60)) {
          $legality=false;
        }


      $deck = customizedDecklist::create([
                                          'name'=> $name,
                                          'cards'=> $cards,
                                          'legality'=> $legality
                                        ]);
              $response = [
                          "data"=> ["name"=> $name,
                                    "cards"=> $cards,
                                    "legality"=> $legality
                                    ]
                          ];

              return response()->json($response,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\customizedDecklist  $customizedDecklist
     * @return \Illuminate\Http\Response
     */
    public function show(customizedDecklist $customizedDecklist)
    {
        //
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
}
