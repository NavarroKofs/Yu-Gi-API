<?php
namespace App;

use App\legalityObject;

class legality
{
  public static function legality($cards){

    $illegalCards = array();
    $legality=true;
    $cardsInDeck=0;


    for ($i=0; $i < count($cards) ; $i++) {

      $legalAmount;
      $cardsInDeck+=$cards[$i]["amount"];
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
    if (($cardsInDeck<40)||($cardsInDeck>60)) {
      $legality=false;
    }

    $answer=new \stdClass;
    $answer->legality = $legality;
    $answer->cardsInDeck = $cardsInDeck;
    $answer->illegalCards = $illegalCards;

      return $answer;
  }
}
 ?>
