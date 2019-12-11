<?php
namespace Tests\Feature;
use App\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CartasTest extends TestCase
{
    //     Search magic card     //
    /** @test */
    public function test_magic_cards(){
        //Given
        $cardData = "raigeki";
        // When
        $response = $this->json('GET', "/api/v1/cards/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
                '*' =>  array("id", "name", "type", "desc",
                "race", "card_sets" => [
                    '*' => [
                        "set_name", "set_code", "set_rarity", "set_price"
                    ]],
                "card_images", "card_prices")
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment([
            'name' => 'Raigeki',
            'type' => "Spell Card",
            'race' => "Normal"
        ]);
    }
    //     Search trap card     //
    /** @test */
    public function test_trap_cards(){
        //Given
        $cardData = "typhoon";
        // When
        $response = $this->json('GET', "/api/v1/cards/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            '*' =>  array("id", "name", "type", "desc",
            "race", "card_sets" => [
                '*' => [
                    "set_name", "set_code", "set_rarity", "set_price"
                ]],
            "card_images", "card_prices")
    ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment([
            'name' => 'Typhoon',
            'type' => 'Trap Card',
            'race' => 'Normal'
        ]);
    }
    //     Search pendulum card     //
    /** @test */
    public function test_pendulum_cards(){
        //Given
        $cardData = "pendulumucho";
        // When
        $response = $this->json('GET', "/api/v1/cards/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
                '*' =>  array("id", "name", "type", "desc", "atk", "def", "level",
                "race", "attribute", "scale", "card_sets" => [
                    '*' => [
                        "set_name", "set_code", "set_rarity", "set_price"
                    ]],
                "card_images", "card_prices")
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment([
            'name' => 'Pendulumucho',
            'type' => 'Pendulum Effect Monster',
            'scale' => '0',
        ]);
    }
    //     Search link card     //
    /** @test */
    public function test_link_cards(){
        //Given
        $cardData = "decode%20talker";
        // When
        $response = $this->json('GET', "/api/v1/cards/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
                '*' =>  array("id", "name", "type", "desc", "atk",
                "race", "attribute", "linkval", "card_sets" => [
                    '*' => [
                        "set_name", "set_code", "set_rarity", "set_price"
                    ]],
                "linkmarkers", "card_images", "card_prices")
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment([
            'name' => 'Decode Talker',
            'type' => 'Link Monster',
            'linkval' => '3'
        ]);
    }
    //     Search xyz card     //
    /** @test */
    public function test_xyz_cards(){
        //Given
        $cardData = "gagaga%20cowboy";
        // When
        $response = $this->json('GET', "/api/v1/cards/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
                '*' =>  array("id", "name", "type", "desc", "atk", "def", "level",
                "race", "attribute", "archetype", "card_sets" => [
                    '*' => [
                        "set_name", "set_code", "set_rarity", "set_price"
                    ]],
                "card_images", "card_prices")
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment([
            'name' => 'Gagaga Cowboy',
            'type' => 'XYZ Monster',
            'level' => '4',
        ]);
    }
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
            'name' => 'Skull Servant',
            'type' => 'Normal Monster',
            'level' => '1'
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
                "current_page", "data", "first_page_url",
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
            "current_page", "data", "first_page_url",
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
            "current_page", "data", "first_page_url",
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
            "current_page", "data", "first_page_url",
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
            "current_page", "data", "first_page_url",
            "from", "last_page",  "last_page_url", "next_page_url",
            "path", "per_page", "prev_page_url", "to", "total"
        ]);
    }
    //     El cliente solicita todas las cartas de un set     //
    /** @test */
    public function test_client_can_request_banlist()
    {
        //Given
        $cardData = "tcg";
        // When
        $response = $this->json('GET', "/api/v1/cards/banlist/$cardData"); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            "current_page", "data", "first_page_url",
            "from", "last_page",  "last_page_url", "next_page_url",
            "path", "per_page", "prev_page_url", "to", "total"
        ]);
    }
    public function test_client_can_request_an_unexistent_banlist()
    {
        //Given
        $cardData = "noviembre";
        // When
        $response = $this->json('GET', "/api/v1/cards/banlist/$cardData"); 
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
                'description' => 'you must enter the banlist "ocg", "tcg" or "goat"'
            ]
        ]);
    }
}