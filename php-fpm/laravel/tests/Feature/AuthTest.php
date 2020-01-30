<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Token;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRegisterUser()
    {
        $user = factory('App\User')->make(['password'=>'secret']);
        $response = $this->post('/api/auth/register',$user->toArray());

        $response->assertStatus(200)
                ->assertJson(['success'=>true]);
        
        $this->assertDatabaseHas('users',['email'=>$user->email,'name'=>$user->name]);
        
    }

    public function testUserCanLogin() {
        $user = factory('App\User')->create();
        
        $response = $this->post('/api/auth/login',['email'=>$user->email, 'password'=>'secret']);

        $response->assertStatus(200)
                ->assertJson(['success'=>true])
                ->assertJsonStructure(['success','token']);
    }

    public function testUserCanLogout() {
        $user = factory('App\User')->create();

        $token = JWTAuth::fromUser($user);

        $response = $this
                        ->withHeaders([
                            'Authorization' => 'Bearer '.$token
                        ])
                        ->post('/api/auth/logout');

        $response->assertStatus(200)
                ->assertExactJson([
                    'success' => true,
                    'message' => 'User logged out successfully'
                ]);
        
        $this->assertGuest('api');
        
    }

}
