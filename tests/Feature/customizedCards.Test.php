<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class customizedCards extends TestCase
{
    use RefreshDatabase;

    const data = [
        'email' => 'kofsmorrizon@gmail.com',
        'name' => "Roberto",
        'stars' => 7,
        "monster-type" => "Winged-Beast",
        "attr" => "Dark",
        "card-type" => "Pendulum",
        "atk" => 3000,
        "def" => 2000,
        "img" => "https://static.wikia.nocookie.net/yugioh/images/7/73/Yugi_muto.png/revision/latest?cb=20170309011846",
        "description" => "El más perrón de aquí"
    ];

    /**
     * CREATE-1
     */
    public function test_store()
    {
        //Given
        $parameters = [
            "data" => self::data
        ];
        // When
        $response = $this->json('POST', 'api/v1/customizedCard', $parameters);
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(201);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            "original" => [
                'data' => [
                    "*" => [
                        "email", "name", "stars", "monster-type", "attr", "card-type", "atk", "def", "img", "description"
                    ]
                ]
            ]
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment(self::data);
        // Assert the database
        // has the data
        $this->assertDatabaseHas(
            'customized_cards', self::data
        );
    }

    public function test_showCards()
    {
        //Create the customized card
        $parameters = [
            "data" => self::data
        ];
        $this->json('POST', 'api/v1/customizedCard', $parameters);
        //Given
        $email = self::data['email'];
        $name = self::data['name'];
        $parameters = "?email=$email&name=$name";
        // When
        $response = $this->json('GET', "api/v1/customizedCard/$parameters");
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'data' => [
                "*" => [
                    "email", "name", "stars", "monster-type", "attr", "card-type", "atk", "def", "img", "description"
                ]
            ]
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment(self::data);
    }

    public function test_updateCard()
    {
        //Create the customized card
        $parameters = [
            "data" => self::data
        ];
        $this->json('POST', 'api/v1/customizedCard', $parameters);
        //Given a modified structure
        $parameters['data']['def'] = 2500;
        // When
        $response = $this->json('PUT', 'api/v1/customizedCard', $parameters);
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'data' => [
                "*" => [
                    "email", "name", "stars", "monster-type", "attr", "card-type", "atk", "def", "img", "description"
                ]
            ]
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment($parameters['data']);
        // Assert the database
        // has the data
        $this->assertDatabaseHas(
            'customized_cards', $parameters['data']
        );
    }

    public function test_removeCard()
    {
        //Create the customized card
        $parameters = [
            "data" => self::data
        ];
        $this->json('POST', 'api/v1/customizedCard', $parameters);
        //Given
        $parameters = [
            "data" => [
                'email' => 'kofsmorrizon@gmail.com',
                'name' => "Roberto"
            ]
        ];
        // When
        $response = $this->json('Delete', 'api/v1/customizedCard', $parameters);
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(200);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'data',
            'total'
        ]);
        // Assert the card has
        // the correct data
        $response->assertJsonFragment([
            'data' => [],
            'total' => 0
        ]);
        // Assert the database
        // has the data
        $this->assertDatabaseMissing(
            'customized_cards', self::data
        );
    }
}