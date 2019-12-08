<?php

namespace Tests\Feature;

use App\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartasTest extends TestCase
{
    //     Search by card name     //
    /** @test */
    public function test_client_can_request_a_card_by_name()
    {
        //Given
        $cardData = "Skull%20Servant";
        // When
        $response = $this->json('GET', "/api/v1/cards/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
                '*' =>  array("id", "name", "type", "desc", "atk", "def", "level",
                "race", "attribute", "archetype", "card_sets",
                "card_images", "card_prices")
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment([
            'name' => 'Skull Servant'
        ]);
    }
    //     Search by content in the name      //
    /** @test */
    public function test_client_can_request_a_card_by_content()
    {
        //Given
        $cardData = "?fname=Skull%20Servant";
        // When
        $response = $this->json('GET', "/api/v1/cards/search$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
                "current_page", "data" => [
                    '*' => [
                        "id", "name", "type", "desc", "atk", "def", "level",
                        "race", "attribute", "archetype", "card_sets",
                        "card_images", "card_prices"
                    ]
                ], "first_page_url",
                "from", "last_page",  "last_page_url", "next_page_url",
                "path", "per_page", "prev_page_url", "to", "total"
        ]);
    }
    //     El servidor no encuentra similitudes con la carta     //
    /** @test */
    public function test_server_does_not_find_the_card_fuzzy(){
        //Given
        $cardData = "?fname=123545485845";
        // When
        $response = $this->json('GET', "/api/v1/cards/search$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(404);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'errors' => [
                'code',
                'title',
                'description'
            ]
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment([
            'errors' => [
                'code' => "ERROR-2",
                'title' => "Not Found",
                'description' => 'No card matching your query was found in the database.'
            ]
        ]);
    }
    //     El cliente no introduce el valor de 'name' o 'fname'     //
    /** @test */
    public function test_server_does_not_find_the_card(){
        //Given
        $cardData = "123545485845";
        // When
        $response = $this->json('GET', "/api/v1/cards/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(404);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'errors' => [
                'code',
                'title',
                'description'
            ]
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment([
            'errors' => [
                'code' => "ERROR-2",
                'title' => "Not Found",
                'description' => 'No card matching your query was found in the database.'
            ]
        ]);
    }
    //     El cliente solicita todas las cartas     //
    /** @test */
    public function test_client_can_request_all_cards()
    {
        // When
        $response = $this->json('GET', "/api/v1/cards/"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            "current_page", "data" => [
                '*' => [
                    "id", "name", "type", "desc", "atk", "def", "level",
                    "race", "attribute", "archetype", "card_sets",
                    "card_images", "card_prices"
                ]
            ], "first_page_url",
            "from", "last_page",  "last_page_url", "next_page_url",
            "path", "per_page", "prev_page_url", "to", "total"
        ]);
    }
    //     El cliente solicita una página específica de todas las cartas     //
    /** @test */
    public function test_client_can_request_one_page_of_all_cards()
    {
        //Given
        $cardData = "?page=2";
        // When
        $response = $this->json('GET', "/api/v1/cards/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            "current_page", "data" => [
                '*' => [
                    "id", "name", "type", "desc", "atk", "def", "level",
                    "race", "attribute", "archetype", "card_sets",
                    "card_images", "card_prices"
                ]
            ], "first_page_url",
            "from", "last_page",  "last_page_url", "next_page_url",
            "path", "per_page", "prev_page_url", "to", "total"
        ]);
    }
    //     El cliente solicita todas las cartas de un set     //
    /** @test */
    public function test_client_can_request_all_cards_of_a_set()
    {
        //Given
        $cardData = "Soul%20Fusion";
        // When
        $response = $this->json('GET', "/api/v1/cards/set/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            "current_page", "data" => [
                '*' => [
                    "id", "name", "type", "desc", "atk", "def", "level",
                    "race", "attribute", "archetype", "card_sets",
                    "card_images", "card_prices"
                ]
            ], "first_page_url",
            "from", "last_page",  "last_page_url", "next_page_url",
            "path", "per_page", "prev_page_url", "to", "total"
        ]);
    }
    //     El cliente no introduce el valor de 'name' o 'fname'     //
    /** @test */
    public function test_server_does_not_find_the_set(){
        //Given
        $cardData = "123545485845";
        // When
        $response = $this->json('GET', "/api/v1/cards/set/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(404);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'errors' => [
                'code',
                'title',
                'description'
            ]
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment([
            'errors' => [
                'code' => "ERROR-2",
                'title' => "Not Found",
                'description' => 'No card matching your query was found in the database.'
            ]
        ]);
    }
    //     El cliente no introduce el valor de 'name' o 'fname'     //
    /** @test */
    public function test_server_does_not_find_the_archetype(){
        //Given
        $cardData = "123545485845";
        // When
        $response = $this->json('GET', "/api/v1/cards/archetype/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(404);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'errors' => [
                'code',
                'title',
                'description'
            ]
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment([
            'errors' => [
                'code' => "ERROR-2",
                'title' => "Not Found",
                'description' => 'No card matching your query was found in the database.'
            ]
        ]);
    }
    //     El cliente solicita todas las cartas de un set     //
    /** @test */
    public function test_client_can_request_all_cards_of_an_archetype()
    {
        //Given
        $cardData = "Blue-Eyes";
        // When
        $response = $this->json('GET', "/api/v1/cards/set/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            "current_page", "data" => [
                '*' => [
                    "id", "name", "type", "desc", "atk", "def", "level",
                    "race", "attribute", "archetype", "card_sets",
                    "card_images", "card_prices"
                ]
            ], "first_page_url",
            "from", "last_page",  "last_page_url", "next_page_url",
            "path", "per_page", "prev_page_url", "to", "total"
        ]);
    }
}