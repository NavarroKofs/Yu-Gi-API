<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\customizedCards;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class customizedCardsController extends Controller
{
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        try {
            $email = $request->data['email'];
            $name = $request->data['name'];
            $validador = customizedCards::select('*')->where('email', $email)->where('name', $name)->get();
            if ($validador->count() > 0) {
                $response = [
                    "error"=> [
                        "code"=> "422",
                        "description" => "Card name already exist"
                    ]
                ];
                return response()->json($response,422);
            }
        } catch (\Throwable $th) {
            $response = [
                "error"=> [
                    "code"=> "422",
                    "description" => "Card name already exist"
                ]
            ];
            return response()->json($response,422);
        }

        try {
            $stars = $request->data['stars'];
            $monsterType = $request->data['monster-type'];
            $attr = $request->data['attr'];
            $cardType = $request->data['card-type'];
            $atk = $request->data['atk'];
            $def = $request->data['def'];
            $img = $request->data['img'];
            $description = $request->data['description'];
            $list = customizedCards::create([
                'email' => $email,
                'name' => $name,
                'stars' => $stars,
                'monster-type' => $monsterType,
                'attr' => $attr,
                'card-type' => $cardType,
                'atk' => $atk,
                'def' => $def,
                'img' => $img,
                'description' => $description
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "errors"=> [
                    "ID"=> "ERROR-1",
                    "title"=>  "Unprocessable Entity",
                    "code" => "422"
                ]
            ], 422);
        }
        //revisar respuesta
        return response()->json(self::getCustomizedCardList($email),201);
    }

     /**
     * get the customized card list.
     *
     * @param  Request  $request
     * @param  string $email
     * @return Response
     */
    private function getCustomizedCardList($email){
        try {
            $cardList = customizedCards::select('*')->where('email', $email)->get();
            $data = collect($cardList);
            $page = request()->has('page') ? request('page') : 1;
            // Set default per page
            $perPage = request()->has('per_page') ? request('per_page') : 15;
            // Offset required to take the results
            $offset = ($page * $perPage) - $perPage;
            $response = self::pagination($data, $offset, $page, $perPage);
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                "errors"=> [
                    "ID"=> "ERR_SHOW-1",
                    "title"=>  "Error in database",
                    "code"=>  "500",
                ]
            ]  , 500);
        }
    }

    public function showCards(Request $request){
        $email = $request->input('email');
        return self::getCustomizedCardList($email);
    }

     /**
     * Remove the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $name
     * @return \Illuminate\Http\Response
     */
    public function removeCard(Request $request)
    {
        try {
            $email = $request->data['email'];
            $name = $request->data['name'];
        } catch (\Throwable $th) {
            return response()->json([
                "errors"=> [
                    "ID"=> "ERROR-1",
                    "title"=>  "Unprocessable Entity",
                    "code" => "422"
                ]
            ], 422);
        }
        try {
            customizedCards::select('*')->where('email', $email)->where('name', $name)->delete();
            return self::getCustomizedCardList($email);
        } catch (\Throwable $th) {
            return response()->json([
                "errors"=> [
                    "ID"=> "REMOVE_CARD-1",
                    "title"=>  "Customized Card not found",
                    "code"=>  "404",
                ]
            ], 404);
        }
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $name
     * @return \Illuminate\Http\Response
     */
    public function updateCard(Request $request)
    {
        try {
            $email = $request->data['email'];
            $name = $request->data['name'];
            $stars = $request->data['stars'];
            $monsterType = $request->data['monster-type'];
            $attr = $request->data['attr'];
            $cardType = $request->data['card-type'];
            $atk = $request->data['atk'];
            $def = $request->data['def'];
            $img = $request->data['img'];
            $description = $request->data['description'];
        } catch (\Throwable $th) {
            return response()->json([
                "errors"=> [
                    "ID"=> "ERROR-1",
                    "title"=>  "Unprocessable Entity",
                    "code" => "422"
                ]
            ], 422);
        }

        try {
            $data = customizedCards::select('*')->where('email', $email)->where('name', $name)->update([
                'name' => $name,
                'stars' => $stars,
                'monster-type' => $monsterType,
                'attr' => $attr,
                'card-type' => $cardType,
                'atk' => $atk,
                'def' => $def,
                'img' => $img,
                'description' => $description
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "errors"=> [
                    "ID"=> "UPDATE_CARD-1",
                    "title"=>  "Bad Request",
                    "code"=>  "400",
                ]
            ], 404);
        }

        return self::getCustomizedCardList($email);
    }

    private function pagination($newCollection, $offset, $page, $perPage){
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
}

/*
{
    "data": {
        	"email": "kofsmorrizon@gmail.com",
            "name": "Roberto Navarro",
            "stars": 7,
            "monster-type": "Winged-Beast",
            "attr": "Dark",
            "card-type": "Pendulum",
            "atk": 3000,
            "def": 2000,
            "img": "https://static.wikia.nocookie.net/yugioh/images/7/73/Yugi_muto.png/revision/latest?cb=20170309011846",
            "description": "El más perrón de aquí"
    }
}
*/