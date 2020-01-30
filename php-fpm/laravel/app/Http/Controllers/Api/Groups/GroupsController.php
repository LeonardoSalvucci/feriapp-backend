<?php

namespace App\Http\Controllers\Api\Groups;

use App\User;
use App\Models\Group;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupsController extends Controller
{
    public function create(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'location' => 'required'
        ]);
        
        try {
            $group = auth()->user()->addGroup($request->all());

            return handleResponseWithData(true, $group);
        } catch(\Exception $e) {
            return handleResponse(false, $e->getMessage());
        }
    }

    public function addUser(Request $request, $group_id) {
        $request['group_id'] = $group_id;
        $this->validate($request, [
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id'
        ]);
        
        $group = Group::find($request->group_id);
        $user = User::find($request->user_id);

        $group->addUser($user);
        return handleResponse(true, "User {$user->name} was added to group {$group->name}");
    }

    public function removeUser(Request $request, $group_id) {
        $request['group_id'] = $group_id;
        $this->validate($request, [
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $group = Group::find($request->group_id);
        $user = User::find($request->user_id);
        
        if(auth()->user()->id==$group->getOwner()->id) {
            $group->removeUser($user);
            return handleResponse(true, "User {$user->name} was removed from group {$group->name}");
        } else {
            return handleResponse(false, "User is now owner of the group");
        }
    }

    public function remove(Request $request, $group_id) {
        $request['group_id'] = $group_id;
        $this->validate($request, [
            'group_id' => 'exists:groups,id'
        ]);
        $group = Group::find($group_id);
        if(auth()->user()->id==$group->getOwner()->id) {
            $group->delete();
            return handleResponse(true,'The group was deleted');
        } else {
            return handleResponse(false,'The user is not owner of the group');
        }
    }

    public function addContact(Request $request, $group_id) {
        $request['group_id'] = $group_id;

        $this->validate($request,[
            'group_id' => 'exists:groups,id',
            //'contact_id' => 'exists:contacts,id'
            'first_name'=>'required',
            'last_name'=>'required',
            'phone'=>'required',
            'mail'=>'required'
        ]);
        $group = Group::find($request->group_id);

        // first verify if the user is part of the group
        if(auth()->user()->hasGroup($group)) {
            $group->addContact($request->all());
            return handleResponse(true, 'Contact was added');
        } else {
            return handleResponse(false,'User is not part of the group');
        }

    }

    public function removeContact(Request $request, $group_id) {
        $request['group_id'] = $group_id;

        $this->validate($request,[
            'group_id' => 'exists:groups,id',
            'contact_id' => 'exists:contacts,id'
        ]);

        $contact = Contact::find($request->contact_id);
        $group = Group::find($request->group_id);
        $group->removeContact($contact);

        return handleResponse(true, 'The contact was removed');
    }
}
