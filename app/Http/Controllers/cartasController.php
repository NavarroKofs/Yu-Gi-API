<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Currency;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class cartasController extends Controller
{
    public function busqueda($cartas){
        $divisor = explode("=", $cartas);
        $instruccion = $divisor[0];
        $nombreCarta = str_replace (" ", "%20", $divisor[1]);
        if ($instruccion == "name"){
            $ruta_base_de_cartas = "https://db.ygoprodeck.com/api/v5/cardinfo.php?name=$nombreCarta";
            return self::crear_JSON($ruta_base_de_cartas);
        }
        if($instruccion == "fname"){
            $ruta_base_de_cartas = "https://db.ygoprodeck.com/api/v5/cardinfo.php?&fname=$nombreCarta";
            return self::paginacion($ruta_base_de_cartas);
        }
    }

    public function crear_JSON($ruta_base_de_cartas){
        $dollar_in_peso = 20;
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
        $ruta_base_de_cartas = "https://db.ygoprodeck.com/api/v5/cardinfo.php?set=$name_set";
        return self::paginacion($ruta_base_de_cartas);
    }

    public function show_banlist($ruling){
        $ruling_type = strtolower($ruling);
        if(($ruling_type == 'ocg') or ($ruling_type == 'tcg') or ($ruling_type == 'goat')){
            $ruta_base_de_cartas = "https://db.ygoprodeck.com/api/v5/cardinfo.php?banlist=$ruling_type";
            return self::paginacion($ruta_base_de_cartas);
        }
    }

    public function show_all_cards_of_archetype($archetype){
        $archetype_name = str_replace (" ", "%20", $archetype);
        $ruta_base_de_cartas = "https://db.ygoprodeck.com/api/v5/cardinfo.php?archetype=$archetype_name";
        return self::paginacion($ruta_base_de_cartas);
    }
}
