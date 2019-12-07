<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Faker\Generator as Faker;

class UserAccountTest extends TestCase
{
        use WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_user_account()
    {
      $email = $this->faker->unique()->safeEmail;

        $userData = [
            'name' => 'Emma',
            'email' => $email,
            'password' => '123456'
        ];

        $response = $this->json('POST', '/api/v1/user', $userData); 
        
        $response->assertStatus(201);
        
        $response->assertJsonStructure([
            'user'=> [
            
            'name',
            'email'
        ]]);
        $response->assertJsonFragment([
            'name' => 'Emma',
            'email' => $email
        ]);

        $body = $response->decodeResponseJson();
       
        $this->assertDatabaseHas(
            'users',
            [
                'email' => $email,
                'name' => 'Emma',
                'password' => '123456'
            ]
        );
    }

    public function test_create_user_account_without_name()
    {
      $email = $this->faker->unique()->safeEmail;

        $userData = [
            'name' => '',
            'email' => $email,
            'password' => '123456'
        ];

        $response = $this->json('POST', '/api/v1/user', $userData); 
        
        $response->assertStatus(422);
        
        $response->assertJsonStructure([
            'errors'=> [
            'name'
        ]]);
        $response->assertJsonFragment([
        //'code'=> '422',
          "message"=>"The given data was invalid.",
        'errors'=>[

          "name"=>["The name field is required."]
        ]
        //'description'=> 'Name missing'
        ]);
    }

    public function test_create_user_account_without_email()
    {
      $email = $this->faker->unique()->safeEmail;

        $userData = [
            'name' => 'Emma',
            'email' => '',
            'password' => '123456'
        ];

        $response = $this->json('POST', '/api/v1/user', $userData); 
        
        $response->assertStatus(422);
        
        $response->assertJsonStructure([
            'errors'=> [
           'email'
        ]]);
        $response->assertJsonFragment([
        //'code'=> '422',
          "message"=>"The given data was invalid.",
        'errors'=>[

          "email"=>["The email must be a valid email address.",
          "The email field is required."
      ]
        ]
        //'description'=> 'Name missing'
        ]);
    }

    public function test_create_user_account_without_password()
    {
      $email = $this->faker->unique()->safeEmail;

        $userData = [
            'name' => 'Emma',
            'email' => $email,
            'password' => ''
        ];

        $response = $this->json('POST', '/api/v1/user', $userData); 
        
        $response->assertStatus(422);
        
        $response->assertJsonStructure([
            'errors'=> [
            'password'
        ]]);
        $response->assertJsonFragment([
        //'code'=> '422',
          "message"=>"The given data was invalid.",
        'errors'=>[

          "password"=>["The password field is required."
      ]
        ]
        //'description'=> 'Name missing'
        ]);
    }

    public function test_reset_password()
    {

        $email1 = $this->faker->unique()->safeEmail;

        $userData1 = [
            'name' => 'Emma',
            'email' => $email1,
            'password' => '123456'
        ];

        $response1 = $this->json('POST', '/api/v1/user', $userData1); 



        $userData = [
            'email' => $email1,

        ];

        $response = $this->json('POST', '/api/v1/sendResetPass', $userData); 
        
        $response->assertStatus(200);
        $msg =  $response->decodeResponseJson();
        $response->assertJsonFragment([
        $msg
        ]);



    }

    public function test_reset_password_for_unregistered_account()
    {

        $email = $this->faker->unique()->safeEmail;
        $userData = [
            'email' => $email,

        ];

        $response = $this->json('POST', '/api/v1/sendResetPass', $userData); 
        
        $response->assertStatus(401);

        $response->assertJsonStructure([
            'errors'=> [
            'code',
            'title',
            'description'
        ]]);

        $response->assertJsonFragment([
        'code'=> '401',
        'title'=> 'Unauthorized',
        'description'=> 'users mail doesnt exists'
        ]);

    }
    public function test_reset_passwordm(){
        $email1 = $this->faker->unique()->safeEmail;

        $userData1 = [
            'name' => 'Emma',
            'email' => $email1,
            'password' => '123456'
        ];

        $response1 = $this->json('POST', '/api/v1/user', $userData1); 



        $userData = [
            'email' => $email1,

        ];

        $response = $this->json('POST', '/api/v1/sendResetPass', $userData); 

        $msg =  $response->decodeResponseJson();

        $userDataUpdate = [
            'token' => $msg,
            'email' => $email1,
            'password' => '1234561'
        ];

        $responseUp = $this->json('POST', '/api/v1/resetPass1', $userDataUpdate);
        $responseUp->assertStatus(200); 

    }

    public function test_reset_password_missing(){
        $email1 = $this->faker->unique()->safeEmail;

        $userData1 = [
            'name' => 'Emma',
            'email' => $email1,
            'password' => '123456'
        ];

        $response1 = $this->json('POST', '/api/v1/user', $userData1); 



        $userData = [
            'email' => $email1,

        ];

        $response = $this->json('POST', '/api/v1/sendResetPass', $userData); 

        $msg =  $response->decodeResponseJson();

        $userDataUpdate = [
            'token' => $msg,
            'email' => $email1,
            'password' => ''
        ];

        $responseUp = $this->json('POST', '/api/v1/resetPass1', $userDataUpdate);
        $responseUp->assertStatus(422); 

        $responseUp->assertJsonStructure([
            'errors'=> [
            'code',
            'title',
            
        ]]);

        $responseUp->assertJsonFragment([
        'code'=> '422',
        'title'=> 'Unprocessable Entity',
        
        ]);
    }

    public function test_reset_email_missing(){
        $email1 = $this->faker->unique()->safeEmail;

        $userData1 = [
            'name' => 'Emma',
            'email' => $email1,
            'password' => '123456'
        ];

        $response1 = $this->json('POST', '/api/v1/user', $userData1); 



        $userData = [
            'email' => $email1,

        ];

        $response = $this->json('POST', '/api/v1/sendResetPass', $userData); 

        $msg =  $response->decodeResponseJson();

        $userDataUpdate = [
            'token' => $msg,
            'email' => '',
            'password' => '1234567'
        ];

        $responseUp = $this->json('POST', '/api/v1/resetPass1', $userDataUpdate);
        $responseUp->assertStatus(422); 

        $responseUp->assertJsonStructure([
            'errors'=> [
            'code',
            'title',
            
        ]]);

        $responseUp->assertJsonFragment([
        'code'=> '422',
        'title'=> 'Unprocessable Entity',
        
        ]);
    }

    public function test_reset_invalid_token(){
        $email1 = $this->faker->unique()->safeEmail;

        $userData1 = [
            'name' => 'Emma',
            'email' => $email1,
            'password' => '123456'
        ];

        $response1 = $this->json('POST', '/api/v1/user', $userData1); 



        $userData = [
            'email' => $email1,

        ];

        $response = $this->json('POST', '/api/v1/sendResetPass', $userData); 

        $msg =  $response->decodeResponseJson();

        $userDataUpdate = [
            'token' => '1234',
            'email' => $email1,
            'password' => '1234567'
        ];

        $responseUp = $this->json('POST', '/api/v1/resetPass1', $userDataUpdate);
        $responseUp->assertStatus(401); 

        $responseUp->assertJsonStructure([
            'errors'=> [
            'code',
            'title',
            'description'
            
        ]]);

        $responseUp->assertJsonFragment([
        'code'=> '401',
        'title'=> 'Unauthorized',
        'description'=> 'Invalid token'
        ]);
    }

    public function test_login1()
    {
     $email1 = $this->faker->unique()->safeEmail;

        $userData1 = [
            'name' => 'Emma',
            'email' => $email1,
            'password' => '123456'
        ];

        $response1 = $this->json('POST', '/api/v1/user', $userData1); 



        $userData = [
            'email' => $email1,

        ];

        $response = $this->json('POST', '/api/v1/sendResetPass', $userData); 

        $msg =  $response->decodeResponseJson();

        $userDataUpdate = [
            'token' => $msg,
            'email' => $email1,
            'password' => '1234561'
        ];

        $responseUp = $this->json('POST', '/api/v1/resetPass1', $userDataUpdate);
       $newRequest = [
        'email'=> $email1,
        'password'=>'1234561'
       ];

        $response1 = $this->json('POST', '/api/v1/login', $newRequest); 

        $response1->assertStatus(200);
     
    }
    public function test_login()
    {
      $email = $this->faker->unique()->safeEmail;

        $userData = [
            'name' => 'Emma',
            'email' => $email,
            'password' => '123456'
        ];

        $response = $this->json('POST', '/api/v1/user', $userData); 

        $body = $response->decodeResponseJson();

        $newRequest =[
            'email' => $email,
            'password' => '12345622' 
        ]; 

        $response1 = $this->json('POST', '/api/v1/login', $newRequest); 

        $response1->assertStatus(401);
        $response1->assertJsonStructure([
            'errors'=> [
            'code',
            'description'
        ]]);

        $response1->assertJsonFragment([
        'code'=> '401',
        'description'=> 'Unauthorized',
        
        ]);
    }
}
