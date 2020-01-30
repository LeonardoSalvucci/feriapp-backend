<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class GroupsTest extends TestCase
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
    public function testUserCanCreateAGroup()
    {
        $group = factory('App\Models\Group')->make();

        $response = $this
                        ->actingAs($this->user)
                        ->post(
                            '/api/group/create',
                            $group->toArray());

        $response->assertStatus(200)
                    ->assertJson(['success'=>true]);

        $group = $response->json()['data'];

        $this->assertCount(1,$this->user->groups);
        $this->assertDatabaseHas('groups',$group);
    }

    public function testUserCanRemoveAGroupIfIsOwner() {
        $group = factory('App\Models\Group')->make();
        $this->user->addGroup($group->toArray());

        $this->assertCount(1,$this->user->groups);

        $response = $this
        ->actingAs($this->user)
        ->delete(
            "/api/group/{$this->user->groups()->first()->id}/remove");

        $response->assertStatus(200)
            ->assertJson(['success'=>true]);
        
        $this->assertDatabaseMissing('groups',$group->toArray());

    }

    public function testUserCantRemoveAGroupIfIsNotOwner() {
        $group = factory('App\Models\Group')->make();
        $this->user->addGroup($group->toArray());

        $user2 = factory('App\User')->create();

        $response = $this
        ->actingAs($user2)
        ->delete(
            "/api/group/{$this->user->groups()->first()->id}/remove");

        $response->assertStatus(200)
            ->assertJson(['success'=>false]);
        
        $this->assertDatabaseHas('groups',$group->toArray());

    }

    public function testUserCanBeAddedToAnExistingGroup() {

        $groupData = factory('App\Models\Group')->make();
        $group = $this->user->addGroup($groupData->toArray());

        $user2 = factory('App\User')->create();

        $response = $this
                        ->actingAs($this->user)
                        ->post(
                            "/api/group/{$group->id}/addUser",
                            ['user_id'=>$user2->id]);

        $response->assertStatus(200)
            ->assertJson(['success'=>true]);

        $this->assertCount(2,$group->users);

        // this is for testing that the user is added just once
        $group->addUser($user2);

        $this->assertCount(2,$group->users);
    }

    public function testRemoveUserFromAGroup() {
        $user2 = factory('App\User')->create();
        $group = $this->user->addGroup(factory('App\Models\Group')->make()->toArray());
        $group->addUser($user2);

        $this->assertCount(1, $this->user->groups);
        $this->assertCount(2, $group->users);

        $response = $this
                ->actingAs($this->user)
                ->post(
                    "/api/group/{$group->id}/removeUser",
                    ['user_id'=>$user2->id]);

        $response->assertStatus(200)
        ->assertJson(['success'=>true]);

        $group = $this->user->groups()->first(); //this call is for sync purpose
        $this->assertCount(1, $group->users);
        $this->assertNotContains($user2->toArray,$group->users->toArray());
    }

    public function testUserCanAddContactToAGroup() {
        $group = $this->user->addGroup(factory('App\Models\Group')->make()->toArray());
        $contact = factory('App\Models\Contact')->make();
        
        $response = $this
                ->actingAs($this->user)
                ->post(
                    "/api/group/{$group->id}/addContact",
                    $contact->toArray());

        $response->assertStatus(200)
        ->assertJson(['success'=>true]);

        $this->assertCount(1, $group->contacts);
        $this->assertDatabaseHas('contacts',$contact->toArray());
    }

    public function testUserCantAddContactToANonHavingGroup() {
        $group = $this->user->addGroup(factory('App\Models\Group')->make()->toArray());
        $contact = factory('App\Models\Contact')->make();
        $user2 = factory('App\User')->create();
        
        $response = $this
                ->actingAs($user2)
                ->post(
                    "/api/group/{$group->id}/addContact",
                    $contact->toArray());

        $response->assertStatus(200)
        ->assertJson(['success'=>false]);

        $this->assertCount(0, $group->contacts);
        $this->assertDatabaseMissing('contacts',$contact->toArray());
    }

    public function testUserCanRemoveAContactFromAGroup() {
        $group = $this->user->addGroup(factory('App\Models\Group')->make()->toArray());
        $contact = factory('App\Models\Contact')->create();
        $group->addContact($contact);

        $this->assertCount(1, $group->contacts);
        $this->assertDatabaseHas('contacts',$contact->toArray());

        $response = $this
                ->actingAs($this->user)
                ->delete(
                    "/api/group/{$group->id}/removeContact",
                    ['contact_id' => $contact->id]);

        $response->assertStatus(200)
            ->assertJson(['success'=>true]);

        $group = $this->user->groups()->first();
        $this->assertCount(0, $group->contacts);
        $this->assertDatabaseMissing('contacts',$contact->toArray());
    }
}
