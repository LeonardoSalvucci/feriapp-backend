<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UsersTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    protected $user;

    public function setUp() :void {
        parent::setUp();

        $this->user = factory('App\User')->create();
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetMyGroups() {
        foreach(factory('App\Models\Group',3)->make() as $group) {
            $this->user->addGroup($group->toArray());
        }
        
        $response = $this
                        ->actingAs($this->user)
                        ->get(
                            "/api/user/getMyGroups");
        $response->assertStatus(200)
            ->assertJson(['success'=>true]);

        $data = $response->json()['data'];
        $this->assertCount(3,$data);
    }

    public function testGetUserGroups() {
        $group1 = factory('App\Models\Group')->make();
        $group2 = factory('App\Models\Group')->make();
        $user2 = factory('App\User')->create();
        $user2->addGroup($group1->toArray());
        $user2->addGroup($group2->toArray());

        $response = $this
                        ->actingAs($this->user)
                        ->get(
                            "/api/user/{$user2->id}/getGroups");
        $response->assertStatus(200)
            ->assertJson(['success'=>true]);

        $data = $response->json()['data'];
        $this->assertCount(2,$data);
        $this->assertArraySubset($group1->toArray(),$data[0]);
        $this->assertArraySubset($group2->toArray(),$data[1]);
    }

    public function testGetMyProfile() {
        $defaultProfile = [
            'user_id' => $this->user->id,
            'first_name' => null,
            'last_name' => null,
            'photo' => '0.jpeg'
        ];

        $response = $this
                        ->actingAs($this->user)
                        ->get(
                            "/api/user/getMyProfile");
        $response->assertStatus(200)
            ->assertJson(['success'=>true]);

        $data = $response->json()['data'];

        $this->assertArraySubset($data,$defaultProfile);
    }

    public function testGetUserProfile() {
        $user2 = factory('App\User')->create();
        $user2profile = factory('App\Models\UserProfile')->create(['user_id'=>$user2->id]);

        $response = $this
                        ->actingAs($this->user)
                        ->get(
                            "/api/user/{$user2->id}/getProfile");
        $response->assertStatus(200)
            ->assertJson(['success'=>true]);

        $data = $response->json()['data'];

        $this->assertEqualsCanonicalizing($data,$user2profile->get()->toArray()[0]);
    }
}
