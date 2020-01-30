<?php

namespace App\Models;

use App\User;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\MockObject\BadMethodCallException;

class Group extends Model
{
    protected $fillable = [
        'name',
        'user_owner_id',
        'location',
        'photo'
    ];

    public function users() {
        return $this->belongsToMany('App\User', 'groups_users');
    }

    public function contacts() {
        return $this->belongsToMAny('App\Models\Contact','contacts_groups');
    }

    public function getOwner() {
        return User::find($this->user_owner_id);
    }

    public function isOwner(User $user = null) { //this method can be called from $user->groups()->isOwner or pass a user to it
        if ($user) {
            return $user->id==$this->user_owner_id;
        }

        if($this->users()->count()==0) {
            throw new BadMethodCallException('This group hasnt got users associated');
        }
        if(array_key_exists('pivot',$this->toArray())) {
            return $this->user_owner_id == $this->pivot->pivotParent->id;
        }
        return false;
    }

    public function changeOwner(User $user) { //Only Owner can assign another owner

        if($this->pivot->pivotParent->id!=$this->user_owner_id) {
            throw new BadMethodCallException('Only owner user can change owner');
        }

        if($this->users()->get()->contains('id',$user->id)) {
            $this->user_owner_id = $user->id;
            $this->save();
            return true;
        } else {
            throw new BadMethodCallException('User not exists in this group');
        }
    }

    public function addContact($attributes) { //verify that only once is added
        if(gettype($attributes) == 'array') {
            $contact = Contact::create($attributes);
            $this->contacts()->attach($contact);
            return $contact;
        } elseif(get_class($attributes) == Contact::class) {
            $this->contacts()->attach($attributes);
            return $attributes;
        }
    }

    public function addManyContacts(array $contacts) {
        $insertedContacts = [];
        foreach($contacts as $contact) {
            $insertedContacts[] = $this->addContact($contact);
        }
        return $insertedContacts;
    }

    public function removeContact(Contact $contact) { // if contact doesn't belongs to any more groups it'll be deleted
        if($this->contacts()->get()->contains('id',$contact->id)) {
            $this->contacts()->detach($contact);
        }
        if($contact->groups()->count()<=0) {
            $contact->delete();
        }
    }

    public function addUser(User $user) {
        if(!$this->users()->get()->contains('id',$user->id)) {
            $this->users()->attach($user);
        }
    }

    public function removeUser(User $user) {

        //Must verify if user is not owner of group
        if($user->id==$this->user_owner_id) {
            throw new BadMethodCallException('User cannot be removed because is owner');
        }
        $this->users()->detach($user);
        return true;
    } 


}
