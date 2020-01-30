<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use App\Models\Group;
use App\Models\Contact;
use Mockery\Exception\BadMethodCallException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelsTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @test
     */
    public function testUserCreate()
    {
        $user = factory('App\User')->create();

        $this->assertDatabaseHas('users',$user->toArray());
    }

    public function testUserCanCreateGroups() {

        $user = factory('App\User')->create();

        $user->groups()->attach(
            factory('App\Models\Group', 3)->create([
                'user_owner_id' => $user->id
            ])
        );

        $this->assertCount(3,$user->groups);

    }

    public function testUserCanAddANewGroup() {
        $user = factory('App\User')->create();

        $group = factory('App\Models\Group')->make();

        $groupInserted = $user->addGroup($group->toArray());
        $this->assertCount(1,$user->groups);
        $this->assertDatabaseHas('groups',$groupInserted->toArray());

    }

    public function testContactCreate() {
        $contact = factory('App\Models\Contact')->create();

        $this->assertDatabaseHas('contacts', $contact->toArray());

    }

    public function testContactShareWithGroupModel() {
        $user = factory('App\User')->create();

        $group = factory('App\Models\Group')->create([
            'user_owner_id' => $user->id
        ]);
        $user->groups()->attach($group);

        $contact = factory('App\Models\Contact')->create();
        $contact->share($group);

        $this->assertCount(1,$group->contacts);
    }

    public function testContactShareWithGroupId() {
        $user = factory('App\User')->create();

        $group = factory('App\Models\Group')->create([
            'user_owner_id' => $user->id
        ]);
        $user->groups()->attach($group);

        $contact = factory('App\Models\Contact')->create();
        $contact->share($group->id);

        $this->assertCount(1,$group->contacts);
    }

    public function testContactShareWithArrayOfGroupsId() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group',3)->create([
                'user_owner_id' => $user->id
            ])
        );
        $this->assertCount(3,$user->groups);

        $contact = factory('App\Models\Contact')->create();
        $contact->share($user->groups()->get()->pluck('id')->all());

        $this->assertCount(3,$contact->groups);

        foreach($user->groups as $group) {
            $this->assertCount(1, $group->contacts);
        }
    }

    public function testUserCanAttachAContactDataToAGroup() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );
        $this->assertCount(1,$user->groups);

        $contactData = factory('App\Models\Contact')->make();

        $contact = $user->groups()->first()->addContact($contactData->toArray());

        $this->assertCount(1,$user->groups()->first()->contacts);
        
        $this->assertDatabaseHas('contacts', $contact->toArray());
    }

    public function testUserCanAttachAContactModelToAGroup() {
        $user = factory('App\User')->create();

        $group 
        = factory('App\Models\Group')->create([
            'user_owner_id' => $user->id
        ]);

        $user->groups()->attach($group);

        $this->assertCount(1,$user->groups);

        $contact = factory('App\Models\Contact')->create();

        $group->addContact($contact);

        $this->assertCount(1,$group->contacts);

    }

    public function testUserCanAttachManyContactsToAGroup() {

        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );
        $this->assertCount(1,$user->groups);

        $contactsData = factory('App\Models\Contact',5)->make();

        $contacts = $user->groups()->first()->addManyContacts($contactsData->toArray());

        $this->assertCount(5,$user->groups()->first()->contacts);

        foreach($contacts as $contact) {
            $this->assertDatabaseHas('contacts', $contact->toArray());
        }

    }

    public function testUserCanDetachAContactFromAGroup() { // and contact deleted because is in only one group
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );
        $this->assertCount(1,$user->groups);

        $contactData = factory('App\Models\Contact')->make();

        $contact = $user->groups()->first()->addContact($contactData->toArray());

        $this->assertCount(1,$user->groups()->first()->contacts);

        $this->assertDatabaseHas('contacts',$contact->toArray());

        $user->groups()->first()->removeContact($contact);

        $this->assertDatabaseMissing('contacts',$contact->toArray());

        $this->assertCount(0,$user->groups()->first()->contacts);

    }

    public function testContactIsNotBeenDeletedWhenIsInMoreThanOneGroup() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group',2)->create([
                'user_owner_id' => $user->id
            ])
        );
        $contact = factory('App\Models\Contact')->create();

        $contact->share($user->groups()->get()->pluck('id')->all());

        $user->groups()->first()->removeContact($contact);

        $this->assertCount(2,$user->groups);
        $this->assertCount(1,$contact->groups);
    }

    public function testGroupGetOwnerFunction() {
        $user = factory('App\User')->create();

        $group = $user->addGroup(factory('App\Models\Group')->make()->toArray());

        $this->assertCount(1,$user->groups);
        $this->assertSame($group->getOwner()->toArray(),$user->get()->except('groups')->toArray()[0]);
    }

    public function testUserCantDetachANonExistingContactFromAGroup() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );
        $this->assertCount(1,$user->groups);

        $contactData = factory('App\Models\Contact')->make();
        $contact = $user->groups()->first()->addContact($contactData->toArray());

        $this->assertCount(1,$user->groups()->first()->contacts);

        $user->groups()->first()->removeContact($contact);

        $this->assertCount(0,$user->groups()->first()->contacts);

        try {
            $user->groups()->first()->removeContact($contact);
        } catch(\BadMethodCallException $e) {

            $this->assertSame($e->getMessage(),'Contact not exists in this group');
        }
    }

    public function testUsercanAttachAUserToAGroupOnlyOnce() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );

        $user2 = factory('App\User')->create();

        $user->groups()->first()->addUser($user2);

        $this->assertCount(2,$user->groups()->first()->users);

        $user->groups()->first()->addUser($user2);

        $this->assertCount(2,$user->groups()->first()->users);
    }

    public function testUserIsOwnerOfAGroup() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );

        $this->assertTrue($user->groups()->first()->isOwner());
    }

    public function testVerifyIfUserIsOwner() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );

        $group = Group::first();
        $this->assertTrue($group->isOwner($user));

        $user2 = factory('App\User')->create();

        $this->assertFalse($group->isOwner($user2));
    }

    public function testUserIsNotOwnerOfAGroup() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );

        $user2 = factory('App\User')->create();

        $user->groups()->first()->addUser($user2);

        $this->assertCount(2,$user->groups()->first()->users);

        $this->assertFalse($user2->groups()->first()->isOwner());

    }

    public function testOwnerUserCanDetachUserToAGroup() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );

        $user2 = factory('App\User')->create();

        $user->groups()->first()->addUser($user2);

        $this->assertCount(2,$user->groups()->first()->users);

        $this->assertTrue($user->groups()->first()->isOwner());

        $user->groups()->first()->removeUser($user2);

        $this->assertCount(1,$user->groups()->first()->users);
    }

    public function testNotOwnerCannotRemoveUserFromAGroup() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );

        $user2 = factory('App\User')->create();

        $user->groups()->first()->addUser($user2);

        $user3 = factory('App\User')->create();

        $user->groups()->first()->addUser($user3);

        $this->assertCount(3,$user->groups()->first()->users);

        $this->assertFalse($user3->groups()->first()->isOwner());

        try {
            $user3->groups()->first()->removeUser($user2);
        } catch(\BadMethodCallException $e) {
            $this->assertSame($e->getMessage(),'User cannot remove users because is not owner');
        }
    }

    public function testUserCannotBeRemovedIfIsOwnerOfTheGroup() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );

        try {
            $user->groups()->first()->removeUser($user);
        } catch(\BadMethodCallException $e) {
            $this->assertSame($e->getMessage(),'User cannot be removed because is owner');
        }
    }

    public function testANonOwnerUserTryToChangeOwnership(){
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );

        $user2 = factory('App\User')->create();

        $user->groups()->first()->addUser($user2);

        try {
            $user2->groups()->first()->changeOwner($user);
        } catch(\BadMethodCallException $e) {
            $this->assertSame($e->getMessage(),'Only owner user can change owner');
        }

    }

    public function testOwnerChangeOwnershipToAnotherUser() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );

        $user2 = factory('App\User')->create();

        $user->groups()->first()->addUser($user2);

        $this->assertTrue($user->groups()->first()->isOwner());
        $this->assertFalse($user2->groups()->first()->isOwner());

        $user->groups()->first()->changeOwner($user2);

        $this->assertTrue($user2->groups()->first()->isOwner());
        $this->assertFalse($user->groups()->first()->isOwner());

    }

    public function testOwnerTryToChangeOwnershipToNonExistingUser() {
        $user = factory('App\User')->create();

        $user->groups()->attach(factory('App\Models\Group')->create([
                'user_owner_id' => $user->id
            ])
        );

        $user2 = factory('App\User')->create();
        
        try {
            $user->groups()->first()->changeOwner($user2);
        } catch(\BadMethodCallException $e) {
            $this->assertSame($e->getMessage(), 'User not exists in this group');
        }
    } 
    
    public function testUserHasGroup() {
        $user = factory('App\User')->create();

        $group = factory('App\Models\Group')->create([
            'user_owner_id' => $user->id
        ]);
        $user->groups()->attach($group);

        $user2 = factory('App\User')->create();

        $this->assertTrue($user->hasGroup($group));
        $this->assertFalse($user2->hasGroup($group));
    }

}
