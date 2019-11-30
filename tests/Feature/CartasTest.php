<?php

namespace Tests\Feature;

use App\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartasTest extends TestCase
{
    //     Search by card name     //
    /** @test */
    /*public function test_client_can_request_a_card_by_name()
    {
        //Given
        $cardData = "name=Skull%20Servant";
        // When
        $response = $this->json('GET', "/api/v1/cartas/$cardData"); 
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
    }*/
    //     Search by content in the name (?)     //
    /** @test */
    /*public function test_client_can_request_a_card_by_content()
    {
        //Given
        $cardData = "fname=Skull%20Servant";
        // When
        $response = $this->json('GET', "/api/v1/cartas/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
                "current_page", "data", "first_page_url",
                "from", "last_page",  "last_page_url", "next_page_url",
                "path", "per_page", "prev_page_url", "to", "total"
        ]);
        // Assert the card has
        // the correct data
        //No sé como representarlo
        $response->assertJsonStructure([
            'data' => [
                "id", "name", "type", "desc", "atk", "def", "level",
                "race", "attribute", "archetype", "card_sets",
                "card_images", "card_prices"]
        ]);
    }*/
    //     El cliente no introduce la palabra 'name' o 'fname'     //
    /** @test */
    /*public function test_client_does_not_enter_the_parameters(){
        //Given
        $cardData = "Skull%20Servant";
        // When
        $response = $this->json('GET', "/api/v1/cartas/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
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
                'code' => "ERROR-1",
                'title' => "Unprocessable Entity",
                'description' => 'expected "name" or "fname" as argument'
            ]
        ]);
    }*/
    //     El cliente no introduce el valor de 'name' o 'fname'     //
    /** @test */
    /*public function test_client_does_not_enter_the_value_of_parameter(){
        //Given
        $cardData = "fname=";
        // When
        $response = $this->json('GET', "/api/v1/cartas/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
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
                'code' => "ERROR-1",
                'title' => "Unprocessable Entity",
                'description' => 'you must enter the name of the card'
            ]
        ]);
    }*/
    //     El cliente no introduce el valor de 'name' o 'fname'     //
    /** @test */
    /*public function test_the_card_is_not_in_database(){
        //Given
        $cardData = "name=perro";
        // When
        $response = $this->json('GET', "/api/v1/cartas/$cardData"); 
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
    }*/
    /*public function test_client_can_request_a_card_by_content()
    {
        //Given
        $cardData = "fname=Skull%20Servant";
        // When
        $response = $this->json('GET', "/api/v1/cartas/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
                "current_page", "data", "first_page_url",
                "from", "last_page",  "last_page_url", "next_page_url",
                "path", "per_page", "prev_page_url", "to", "total"
        ]);
        // Assert the card has
        // the correct data
        //No sé como representarlo
        /*$response->assertJsonStructure([
            'data' => [
                "id", "name", "type", "desc", "atk", "def", "level",
                "race", "attribute", "archetype", "card_sets",
                "card_images", "card_prices"]
        ]);
    }*/
}
