<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ContactsTest extends TestCase
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
    public function testContactCreate()
    {
        $contact = factory('App\Models\Contact')->make();

        $response = $this
                        ->actingAs($this->user)
                        ->post(
                            '/api/contact/create',
                            $contact->toArray());

        $response->assertStatus(200)
                    ->assertJson(['success'=>true]);
        $data = $response->json()['data'];

        $this->assertDatabaseHas('contacts',$contact->toArray());
        $this->assertArraySubset($contact->toArray(),$data);
    }

    public function testContactShareWithGroupID() {
        $contact = factory('App\Models\Contact')->create();
        $group = $this->user->addGroup(factory('App\Models\Group')->make()->toArray());

        $response = $this
                        ->actingAs($this->user)
                        ->post(
                            '/api/contact/'.$contact->id.'/share',
                            ['share_to' => $group->id]);

        $response->assertStatus(200)
                    ->assertJson(['success'=>true]);

        $this->assertCount(1,$this->user->groups()->first()->contacts);

    }

    public function testContactShareWithManyGroups() {
        $contact = factory('App\Models\Contact')->create();
        $groups = collect();

        for($i=0;$i<3;$i++) {
            $groups->add($this->user->addGroup(factory('App\Models\Group')->make()->toArray()));
        }

        $response = $this
                        ->actingAs($this->user)
                        ->post(
                            '/api/contact/'.$contact->id.'/share',
                            ['share_to' => $groups->pluck('id')->all()]);

        $response->assertStatus(200)
                    ->assertJson(['success'=>true]);

        $this->assertCount(3,$contact->groups);
        
    }

    public function testRemoveContactFromAllGroups() {
        $contact = factory('App\Models\Contact')->create();
        $groups = collect();

        for($i=0;$i<3;$i++) {
            $groups->add($this->user->addGroup(factory('App\Models\Group')->make()->toArray()));
        }
        $contact->share($groups->pluck('id')->all());

        $this->assertCount(3,$contact->groups);
        
        $response = $this
        ->actingAs($this->user)
        ->delete(
            '/api/contact/'.$contact->id.'/remove');

        $response->assertStatus(200)
            ->assertJson(['success'=>true]);

        $this->assertDatabaseMissing('contacts', $contact->get()->except('groups')->toArray());

    }
}
