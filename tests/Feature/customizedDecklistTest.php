<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class customizedDecklistTest extends TestCase
{
  use RefreshDatabase;
  /**
   * CREATE-1
   */
  public function test_create()
  {
      $response = $this->json('DELETE', 'api/v1/decklist/Ghostrick');
      $deckListData = [
                      "data"=> ["name"=> "Ghostrick",
                                "cards"=>[[
                                              "name"=> "Ghostrick lantern",
                                              "amount"=> 3
                                            ],[
                                              "name"=> "Ghostrick ghoul",
                                              "amount"=> 3
                                            ]]
                                ]
                      ];

      $response = $this->json('POST', 'api/v1/decklist/', $deckListData);

      $response->assertStatus(201);
      $response->assertJsonFragment([
          'name' => 'Ghostrick',
          "name"=> "Ghostrick lantern",
          "name"=> "Ghostrick ghoul",
          "amount"=> 3
      ]);
      $this->assertDatabaseHas(
          'customized_decklists',
          [
              'name' => 'Ghostrick'
          ]
      );

  }

  /**
   * CREATE-2
   */
  public function test_create_same_name()
  {
      $deckListData = [
                      "data"=> ["name"=> "Ghostrick",
                                "cards"=>[[
                                              "name"=> "Ghostrick lantern",
                                              "amount"=> 3
                                            ],[
                                              "name"=> "Ghostrick ghoul",
                                              "amount"=> 3
                                            ]]
                                ]
                      ];

      $response = $this->json('POST', 'api/v1/decklist/', $deckListData);
      $response = $this->json('POST', 'api/v1/decklist/', $deckListData);

      $response->assertStatus(422);
      $response->assertJsonFragment([
          "description" => "Deck name already taken",
      ]);

      $response = $this->json('DELETE', 'api/v1/decklist/Ghostrick');
  }

  /**
   * DELETE-1
   */
  public function test_delete()
  {

      $deckListData = [
                      "data"=> ["name"=> "Ghostrick",
                                "cards"=>[[
                                              "name"=> "Ghostrick lantern",
                                              "amount"=> 3
                                            ],[
                                              "name"=> "Ghostrick ghoul",
                                              "amount"=> 3
                                            ]]
                                ]
                      ];

      $response = $this->json('POST', 'api/v1/decklist/', $deckListData);
      $response = $this->json('DELETE', 'api/v1/decklist/Ghostrick');

      $response->assertStatus(204);

  }

  /**
   * DELETE-2
   */
  public function test_delete_not_found()
  {

      $response = $this->json('DELETE', 'api/v1/decklist/Chihuahua');

      $response->assertStatus(404);
      $response->assertJsonFragment([
          "title"=>  "Decklist not found"
      ]);


  }

  /**
  * PUT-1
  */

  public function test_addCard()
  {

      $deckListData = [
                    "data"=> ["name"=> "Ghostrick",
                              "cards"=>[[
                                            "name"=> "Ghostrick lantern",
                                            "amount"=> 3
                                          ],[
                                            "name"=> "Ghostrick ghoul",
                                            "amount"=> 3
                                          ]]
                              ]
                    ];

      $response = $this->json('POST', 'api/v1/decklist/', $deckListData);


      $cards = [
                      "data"=> ["cards"=>[[
                                            "name"=> "Raigeki",
                                            "amount"=> 3
                                          ]]
                                ]
                      ];

      $response = $this->json('PUT', 'api/v1/decklist/Ghostrick', $cards);

      $response->assertStatus(200);

      $response->assertJsonFragment([
          'name' => 'Ghostrick',
          "name"=> "Ghostrick lantern",
          "name"=> "Ghostrick ghoul",
          "name"=> "Raigeki",
          "amount"=> 3
      ]);
      $this->assertDatabaseHas(
          'customized_decklists',
          [
              'name' => 'Ghostrick'
          ]
      );

      $response = $this->json('DELETE', 'api/v1/decklist/Ghostrick');

  }

  /**
  * PUT-2
  */

  public function test_addCard_deck_not_found()
  {

      $cards = [
                      "data"=> ["cards"=>[[
                                            "name"=> "Raigeki",
                                            "amount"=> 3
                                          ]]
                                ]
                      ];

      $response = $this->json('PUT', 'api/v1/decklist/Chihuahua', $cards);

      $response->assertStatus(404);

      $response->assertJsonFragment([
          "title"=>  "Decklist not found"
      ]);

  }

  /**
  * PUT-3
  */

  public function test_addCard_cards_not_found()
  {

      $deckListData = [
                    "data"=> ["name"=> "Ghostrick",
                              "cards"=>[[
                                            "name"=> "Ghostrick lantern",
                                            "amount"=> 3
                                          ],[
                                            "name"=> "Ghostrick ghoul",
                                            "amount"=> 3
                                          ]]
                              ]
                    ];

      $response = $this->json('POST', 'api/v1/decklist/', $deckListData);
      $cards = [
                      "data"=> []
                      ];

      $response = $this->json('PUT', 'api/v1/decklist/Ghostrick', $cards);

      $response->assertStatus(422);

      $response->assertJsonFragment([
          "title"=>  "Cards not found"
      ]);

      $response = $this->json('DELETE', 'api/v1/decklist/Ghostrick');

  }

  /**
  *   Remove-1
  **/

  public function test_removeCard()
  {

      $deckListData = [
                    "data"=> ["name"=> "Ghostrick",
                              "cards"=>[[
                                            "name"=> "Ghostrick lantern",
                                            "amount"=> 3
                                          ],[
                                            "name"=> "Ghostrick ghoul",
                                            "amount"=> 3
                                          ]]
                              ]
                    ];

      $response = $this->json('POST', 'api/v1/decklist/', $deckListData);

      $response = $this->json('DELETE', 'api/v1/decklist/Ghostrick/Raigeki');

      $response->assertStatus(200);

      $response->assertJsonFragment([
          'name' => 'Ghostrick',
          "name"=> "Ghostrick lantern",
          "name"=> "Ghostrick ghoul",
          "amount"=> 3
      ]);
      $this->assertDatabaseHas(
          'customized_decklists',
          [
              'name' => 'Ghostrick'
          ]
      );

      $response = $this->json('DELETE', 'api/v1/decklist/Ghostrick');

  }

  /**
  *   Remove-2
  **/

  public function test_removeCard_deck_not_found()
  {

      $response = $this->json('DELETE', 'api/v1/decklist/Ghostrick/Raigeki');

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

      $deckListData = [
                    "data"=> ["name"=> "Ghostrick",
                              "cards"=>[[
                                            "name"=> "Ghostrick lantern",
                                            "amount"=> 3
                                          ],[
                                            "name"=> "Ghostrick ghoul",
                                            "amount"=> 3
                                          ]]
                              ]
                    ];

      $response = $this->json('POST', 'api/v1/decklist/', $deckListData);

      $response = $this->json('GET', 'api/v1/decklist/Ghostrick');

      $response->assertStatus(200);

      $response->assertJsonFragment([
          'name' => 'Ghostrick',
          "name"=> "Ghostrick lantern",
          "name"=> "Ghostrick ghoul",
          "amount"=> 3
      ]);

      $response = $this->json('DELETE', 'api/v1/decklist/Ghostrick');

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
