<?php

namespace Tests\Feature;

use App\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class customizedWishListTest extends TestCase
{
  /**
   * CREATE-1
   */
  public function test_create()
  {
      $WishListData = [
                      'data'=> ['name'=> "WL1",
                                'cards'=>["skull servant", "subterror guru"]
                                ]
                      ];
      $response = $this->json('POST', 'v1/wishlist/create', $WishListData);
      $response->assertStatus(201);
      $response->assertJsonFragment([
          'name' => 'WL1',
          "cards"=> [
                "skull servant",
                "subterror guru"
            ],
            "price" => 34.028781
      ]);
      $this->assertDatabaseHas(
          'customized_wish_list',
          [
              'name' => 'WL1'
          ]
      );
      $response = $this->json('DELETE', 'v1/wishlist/1');
  }
  /**
   * DELETE-1
   */
  public function test_delete()
  {
    $WishListData = [
        "data"=> ["name"=> "WL1",
                  "cards"=>["subterror guru", "skull servant"]
                  ]
        ];
      $response = $this->json('POST', 'v1/wishlist/create', $WishListData);   
      $response = $this->json('DELETE', 'v1/wishlist/2');
      $response->assertStatus(204);
  }
  /**
   * DELETE-2
   */
  public function test_delete_not_found()
  {
      $response = $this->json('DELETE', 'v1/wishlist/666');
      $response->assertStatus(404);
      $response->assertJsonFragment([
          "title"=>  "WishList not found"
      ]);
  }
  /**
  * PUT-1
  */
  public function test_addCard()
  {
      $WishListData = [
        "data"=> ["name"=> "WL1",
                  "cards"=>["subterror guru", "skull servant"]
                  ]
      ];
      $response = $this->json('POST', 'v1/wishlist/create', $WishListData);

      $cards = [
                "cards" => ["niwatori"]
               ];

      $response = $this->json('PUT', 'v1/wishlist/3', $cards);
      $response->assertStatus(200);
      $response->assertJsonFragment([
        "id"=> 3,
        "created_at" => "2019-12-09 23:13:57",
        "updated_at" => "2019-12-12 02:13:07",
        "name" => "WL1",
        "cards" => [
            "Subterror guru",
            "Skull servant",
            "niwatori"
        ],
        "price"=> 36.912576
      ]);
      $this->assertDatabaseHas(
          'customized_decklists',
          [
              'name' => 'WL1'
          ]
      );
      $response = $this->json('DELETE', 'v1/wishlist/3');
  }
  /**
  * PUT-2
  */
  public function test_addCard_deck_not_found()
  {
    $cards = [
      "cards" => ["niwatori"]
    ];

    $response = $this->json('PUT', 'v1/wishlist/666', $cards);
    $response->assertStatus(404);
    $response->assertJsonFragment([
        "title"=>  "Decklist not found"
    ]);
  }

  /**
  *   Tottal Price-1
  **/

  public function test_total_price()
  {
      $WishListData = [
        "data"=> ["name"=> "WL1",
                  "cards"=>["subterror guru", "skull servant"]
                  ]
        ];
      $response = $this->json('POST', 'v1/wishlist/create', $WishListData);   
      $response = $this->json('GET', 'v1/wishlist/tPrice/4');
      $response->assertStatus(200);
      $response->assertJsonFragment([
        "price"=> 34.028781
      ]);
      $response = $this->json('DELETE', 'v1/wishlist/4');
  }

  /**
   * Tottal Price-2
   */
  public function test_total_price_not_found()
  {
      $response = $this->json('GET', 'v1/wishlist/tPrice/666');
      $response->assertStatus(404);
      $response->assertJsonFragment([
          "title"=>  "WishList not found"
      ]);
  }

  /**
  *   Remove-1
  **/
  public function test_removeCard()
  {   
      $WishListData = [
        "data"=> ["name"=> "WL1",
                  "cards"=>["subterror guru", "skull servant"]
                  ]
        ];
      $response = $this->json('POST', 'v1/wishlist/create', $WishListData);   
      $card = 'subterror guru';
      $response = $this->json('DELETE', "v1/wishlist/rCard/5/$card");
      $response->assertStatus(200);
      $response->assertJsonFragment([
        "id" => 4,
        "created_at" => "2019-12-12 02:03:06",
        "updated_at" => "2019-12-12 02:37:36",
        "name" => "WL1",
        "cards" => [
            "Skull servant"
        ],
        "price" => 15.764745999999999
      ]);
      $this->assertDatabaseHas(
          'customized_decklists',
          [
              'name' => 'wl1'
          ]
      );
      $response = $this->json('DELETE', 'v1/wishlist/5');
  }
  /**
  *   Remove-2
  **/
  public function test_removeCard_deck_not_found()
  {
      $card = 'subterror guru';
      $response = $this->json('DELETE', "v1/wishlist/rCard/666/$card");
      $response->assertStatus(404);
      $response->assertJsonFragment([
          "title"=>  "Decklist not found"
      ]);
  }
  /**
  *   View-1
  **/
  public function test_viewDecklist()
  {
      $WishListData = [
        "data"=> ["name"=> "WL1",
                  "cards"=>["subterror guru", "skull servant"]
                  ]
        ];
      $response = $this->json('POST', 'v1/wishlist/create', $WishListData);  
      $response = $this->json('GET', 'v1/wishlist/6');
      $response->assertStatus(200);
      $response = $this->json('DELETE', 'v1/wishlist/6');
  }
  /**
  *   View-2
  **/
  public function test_viewDecklist_deck_not_found()
  {
      $response = $this->json('GET', 'api/v1/decklist/Ghostrick');
      $response->assertStatus(404);
      $response->assertJsonFragment([
           "title"=>  "Decklist not found"
      ]);
  }
}