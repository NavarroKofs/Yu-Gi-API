<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Currency;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class cardsController extends Controller
{
    public function searchByName($request){
        $cardName = str_replace (" ", "%20", $request);
        $cardDatabasePath = "https://db.ygoprodeck.com/api/v5/cardinfo.php?name=$cardName";
        $validation = self::getStatus($cardDatabasePath);
        if ($validation != '200') {
            return response()->json([
                "errors"=> ["code"=> "ERROR-2",
                "title"=>  "Not Found",
                "description"=> 'No card matching your query was found in the database.'
                ]]  , 404); 
        }
        $dollar_in_peso = self::getPrice();
        $cardInfo = self::getContent($cardDatabasePath);
        $data = self::createJSON($cardInfo, $dollar_in_peso);
        return $data;
    }

    public function fuzzySearch(Request $request){
        $cardName = str_replace (" ", "%20", $request->input('fname'));
        $cardDatabasePath = "https://db.ygoprodeck.com/api/v5/cardinfo.php?&fname=$cardName";
        $validation = self::getStatus($cardDatabasePath);
        if ($validation != '200') {
            return response()->json([
                "errors"=> ["code"=> "ERROR-2",
                "title"=>  "Not Found",
                "description"=> 'No card matching your query was found in the database.'
                ]]  , 404); 
        }
        $dollar_in_peso = self::getPrice();
        $cardInfo = self::getContent($cardDatabasePath);
        $data = collect(self::createJSON($cardInfo, $dollar_in_peso));
        // Set default page
        $page = request()->has('page') ? request('page') : 1;
        // Set default per page
        $perPage = request()->has('per_page') ? request('per_page') : 15;
        // Offset required to take the results
        $offset = ($page * $perPage) - $perPage;
        return self::pagination($data, $offset, $page, $perPage);
    }

    public function createJSON($cardInfo, $dollar_in_peso){
        $num_cards = count($cardInfo);
        $rules = array(
            'card_sets' => 'required',
        );
        for($carta=0; $carta<$num_cards; $carta++){
            $validation = Validator::make($cardInfo[$carta], $rules);            
            if (!$validation->fails()){
                $num_precios = count($cardInfo[$carta]['card_sets']);
                for ($precio=0; $precio<$num_precios; $precio++){
                    $priceUSD = $cardInfo[$carta]['card_sets'][$precio]['set_price'];
                    $priceMXN = round($priceUSD * $dollar_in_peso, 2);
                    $cardInfo[$carta]['card_sets'][$precio]['set_price'] = "$".$priceMXN. " MXN";
                }
            }
        }
        return $cardInfo;
    }

    public function showAllCards(){
        $cardDatabasePath = "https://db.ygoprodeck.com/api/v5/cardinfo.php";
        $validation = self::getStatus($cardDatabasePath);
        if ($validation != '200') {
            return response()->json([
                "errors"=> ["code"=> "ERROR-2",
                "title"=>  "Not Found",
                "description"=> 'No card matching your query was found in the database.'
                ]]  , 404); 
        }
        $dollar_in_peso = self::getPrice();
        $cardInfo = self::getContent($cardDatabasePath);
        $data = collect(self::createJSON($cardInfo, $dollar_in_peso));
        // Set default page
        $page = request()->has('page') ? request('page') : 1;
        // Set default per page
        $perPage = request()->has('per_page') ? request('per_page') : 15;
        // Offset required to take the results
        $offset = ($page * $perPage) - $perPage;
        return self::pagination($data, $offset, $page, $perPage);
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

    public function showAllCardsOfASet($set){
        $name_set = str_replace (" ", "%20", $set);
        if ($name_set == ""){
            return response()->json([
                "errors"=> ["code"=> "ERROR-1",
                "title"=>  "Unprocessable Entity",
                "description"=> 'you must enter the name of the set'
                ]]  , 422);
        }
        $cardDatabasePath = "https://db.ygoprodeck.com/api/v5/cardinfo.php?set=$name_set";
        $validation = self::getStatus($cardDatabasePath);
        if ($validation != '200') {
            return response()->json([
                "errors"=> ["code"=> "ERROR-2",
                "title"=>  "Not Found",
                "description"=> 'No card matching your query was found in the database.'
                ]]  , 404); 
        }
        $dollar_in_peso = self::getPrice();
        $cardInfo = self::getContent($cardDatabasePath);
        $data = collect(self::createJSON($cardInfo, $dollar_in_peso));
        // Set default page
        $page = request()->has('page') ? request('page') : 1;
        // Set default per page
        $perPage = request()->has('per_page') ? request('per_page') : 15;
        // Offset required to take the results
        $offset = ($page * $perPage) - $perPage;
        return self::pagination($data, $offset, $page, $perPage);
    }

    public function showBanlist($ruling){
        $ruling_type = strtolower($ruling);
        if(($ruling_type == 'ocg') or ($ruling_type == 'tcg') or ($ruling_type == 'goat')){
            $cardDatabasePath = "https://db.ygoprodeck.com/api/v5/cardinfo.php?banlist=$ruling_type";
            $validation = self::getStatus($cardDatabasePath);
            if ($validation != '200') {
                return response()->json([
                    "errors"=> ["code"=> "ERROR-2",
                    "title"=>  "Not Found",
                    "description"=> 'No card matching your query was found in the database.'
                    ]]  , 404); 
            }
            $dollar_in_peso = self::getPrice();
            $cardInfo = self::getContent($cardDatabasePath);
            $data = collect(self::createJSON($cardInfo, $dollar_in_peso));
            // Set default page
            $page = request()->has('page') ? request('page') : 1;
            // Set default per page
            $perPage = request()->has('per_page') ? request('per_page') : 15;
            // Offset required to take the results
            $offset = ($page * $perPage) - $perPage;
            return self::pagination($data, $offset, $page, $perPage);
        }
        return response()->json([
            "errors"=> ["code"=> "ERROR-1",
            "title"=>  "Unprocessable Entity",
            "description"=> 'you must enter the banlist "ocg", "tcg" or "goat"'
            ]]  , 422);
    }

    public function showAllCardsOfAnArchetype($archetype){
        if ($archetype == ""){
            return response()->json([
                "errors"=> ["code"=> "ERROR-4",
                "title"=>  "Bad Request",
                "description"=> 'you must enter the name of the archetype'
                ]]  , 422);
        }
        $archetype_name = str_replace (" ", "%20", $archetype);
        $cardDatabasePath = "https://db.ygoprodeck.com/api/v5/cardinfo.php?archetype=$archetype_name";
        $validation = self::getStatus($cardDatabasePath);
        if ($validation != '200') {
            return response()->json([
                "errors"=> ["code"=> "ERROR-2",
                "title"=>  "Not Found",
                "description"=> 'No card matching your query was found in the database.'
                ]]  , 404); 
        }
        $dollar_in_peso = self::getPrice();
        $cardInfo = self::getContent($cardDatabasePath);
        $data = collect(self::createJSON($cardInfo, $dollar_in_peso));
        // Set default page
        $page = request()->has('page') ? request('page') : 1;
        // Set default per page
        $perPage = request()->has('per_page') ? request('per_page') : 15;
        // Offset required to take the results
        $offset = ($page * $perPage) - $perPage;
        return self::pagination($data, $offset, $page, $perPage);
    }

    public function getStatus($url){
        $headers = get_headers($url);
        $status = substr($headers[0], 9, 3);
        return $status;
    }

    public function getContent($cardDatabasePath){
        $cardJson = file_get_contents($cardDatabasePath);
        $cardInfo = json_decode($cardJson, true);
        return $cardInfo;
    }

    public function getPrice(){
        return DB::table('currencies')->whereId(1)->first()->valor;
    }
}
