<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Currency;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class cartasController extends Controller
{
    public function busqueda($cartas){
        $divisor = explode("=", $cartas);
        if($cartas == $divisor[0]){
            return response()->json([
                "errors"=> ["code"=> "ERROR-1",
                "title"=>  "Unprocessable Entity",
                "description"=> 'expected "name" or "fname" as argument'
                ]]  , 422);
        }
        $instruccion = $divisor[0];
        $nombreCarta = str_replace (" ", "%20", $divisor[1]);
        if ($nombreCarta == ""){
            return response()->json([
                "errors"=> ["code"=> "ERROR-1",
                "title"=>  "Unprocessable Entity",
                "description"=> 'you must enter the name of the card'
                ]]  , 422);
        }
        if ($instruccion == "name"){
            $ruta_base_de_cartas = "https://db.ygoprodeck.com/api/v5/cardinfo.php?name=$nombreCarta";
            return response()->json(self::crear_JSON($ruta_base_de_cartas), 200);
        }
        if($instruccion == "fname"){
            $ruta_base_de_cartas = "https://db.ygoprodeck.com/api/v5/cardinfo.php?&fname=$nombreCarta";
            return response()->json(self::paginacion($ruta_base_de_cartas), 200);
        }
        return response()->json([
            "errors"=> ["code"=> "ERROR-1",
            "title"=>  "Unprocessable Entity",
            "description"=> 'expected "name" or "fname" as argument'
            ]]  , 422); 
    }

    public function crear_JSON($ruta_base_de_cartas){
        $dollar_in_peso = DB::table('currencies')->whereId(1)->first()->valor;
        $headers = get_headers($ruta_base_de_cartas);
        $status = substr($headers[0], 9, 3);
        if ($status != '200') {
            return response()->json([
                "errors"=> ["code"=> "ERROR-2",
                "title"=>  "Not Found",
                "description"=> 'No card matching your query was found in the database.'
                ]]  , 404); 
        }
        $cardJson = file_get_contents($ruta_base_de_cartas);
        $cardInfo = json_decode($cardJson, true);
        $num_cards = count($cardInfo);
        $rules = array(
            'card_sets' => 'required',
        );
        for($carta=0; $carta<$num_cards; $carta++){
            $validacion = Validator::make($cardInfo[$carta], $rules);            
            if (!$validacion->fails()){
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

    public function show_all_cards(){
        $ruta_base_de_cartas = "https://db.ygoprodeck.com/api/v5/cardinfo.php";
        $headers = get_headers($ruta_base_de_cartas);
        $status = substr($headers[0], 9, 3);
        if ($status != '200') {
            return response()->json([
                "errors"=> ["code"=> "ERROR-3",
                "title"=>  "Service Unavailable",
                "description"=> 'The server is currently unable to handle the request due to a temporary 
                overload or scheduled maintenance, which will likely be alleviated after some delay.'
                ]]  , 503); 
        }
        return self::paginacion($ruta_base_de_cartas);
    }

    public function paginacion($ruta_base_de_cartas){
        // Set default page
        $page = request()->has('page') ? request('page') : 1;
        // Set default per page
        $perPage = request()->has('per_page') ? request('per_page') : 15;
        // Offset required to take the results
        $offset = ($page * $perPage) - $perPage;
        // At here you might transform your data into collection
        $headers = get_headers($ruta_base_de_cartas);
        $status = substr($headers[0], 9, 3);
        if ($status != '200') {
            return response()->json([
                "errors"=> ["code"=> "ERROR-2",
                "title"=>  "Not Found",
                "description"=> 'No card matching your query was found in the database.'
                ]]  , 404); 
        }
        $newCollection = collect(self::crear_JSON($ruta_base_de_cartas));
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

    public function show_all_cards_of_a_set($set){
        $name_set = str_replace (" ", "%20", $set);
        if ($name_set == ""){
            return response()->json([
                "errors"=> ["code"=> "ERROR-1",
                "title"=>  "Unprocessable Entity",
                "description"=> 'you must enter the name of the card'
                ]]  , 422);
        }
        $ruta_base_de_cartas = "https://db.ygoprodeck.com/api/v5/cardinfo.php?set=$name_set";
        $headers = get_headers($ruta_base_de_cartas);
        $status = substr($headers[0], 9, 3);
        if ($status != '200') {
            return response()->json([
                "errors"=> ["code"=> "ERROR-2",
                "title"=>  "Not Found",
                "description"=> 'No card matching your query was found in the database.'
                ]]  , 404); 
        }
        return self::paginacion($ruta_base_de_cartas);
    }

    public function show_banlist($ruling){
        $ruling_type = strtolower($ruling);
        if(($ruling_type == 'ocg') or ($ruling_type == 'tcg') or ($ruling_type == 'goat')){
            $ruta_base_de_cartas = "https://db.ygoprodeck.com/api/v5/cardinfo.php?banlist=$ruling_type";
            return self::paginacion($ruta_base_de_cartas);
        }
        return response()->json([
            "errors"=> ["code"=> "ERROR-1",
            "title"=>  "Unprocessable Entity",
            "description"=> 'you must enter the banlist "ocg", "tcg" or "goat"'
            ]]  , 422);
    }

    public function show_all_cards_of_archetype($archetype){
        $archetype_name = str_replace (" ", "%20", $archetype);
        if ($archetype_name == ""){
            return response()->json([
                "errors"=> ["code"=> "ERROR-1",
                "title"=>  "Unprocessable Entity",
                "description"=> 'you must enter the name of the card'
                ]]  , 422);
        }
        $ruta_base_de_cartas = "https://db.ygoprodeck.com/api/v5/cardinfo.php?archetype=$archetype_name";
        $headers = get_headers($ruta_base_de_cartas);
        $status = substr($headers[0], 9, 3);
        if ($status != '200') {
            return response()->json([
                "errors"=> ["code"=> "ERROR-2",
                "title"=>  "Not Found",
                "description"=> 'No card matching your query was found in the database.'
                ]]  , 404); 
        }
        return self::paginacion($ruta_base_de_cartas);
    }
}
