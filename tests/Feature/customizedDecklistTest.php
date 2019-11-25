<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class customizedDecklistTest extends TestCase
{
  /**
   * CREATE-1
   */
  public function test_create()
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
   * DELETE-1
   */
  public function test_delete()
  {
      $deckListData = [
                      "data"=> ["name"=> "Ghostrick"
                                ]
                      ];

      $response = $this->json('DELETE', 'api/v1/decklist/', $deckListData);

      $response->assertStatus(204);

  }
/*
  public function test_addCard()
  {
      $deckListData = [
                    "data"=> ["name"=> "Ghostrick",
                              "cards"=>[]
                              ]
                    ];

      $this->json('POST', 'api/v1/decklist/', $deckListData);

      $cards = [
                      "data"=> ["cards"=>[[
                                    "name"=> "Ghostrick lantern",
                                    "amount"=> 3
                                  ],[
                                    "name"=> "Ghostrick ghoul",
                                    "amount"=> 3
                                  ]]
                                ]
                      ];

      $response = $this->json('POST', 'api/v1/decklist/Ghostrick', $cards);

      $response->assertStatus(200);
      $response->assertJsonFragment([
          'name' => 'Ghostrick',
          "name"=> "Ghostrick lantern",
          "amount"=> 3
      ]);

      $deckListData = [
                      "data"=> ["name"=> "Ghostrick"
                                ]
                      ];

      $this->json('DELETE', 'api/v1/decklist/', $deckListData);


  }
  */
}
