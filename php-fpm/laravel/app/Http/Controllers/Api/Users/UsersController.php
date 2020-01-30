<?php

namespace App\Http\Controllers\Api\Users;

use App\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function getMyGroups() {
        return handleResponseWithData(true,auth()->user()->groups->toArray());
    }

    public function getGroups(Request $request, $user_id) {
        $request['user_id'] = $user_id;
        $this->validate($request, [
            'user_id' => 'exists:users,id'
        ]);

        return handleResponseWithData(true,User::find($user_id)->groups);
    }

    public function getMyProfile() {
        return handleResponseWithData(true, auth()->user()->profile);
    }

    public function getProfile(Request $request, $user_id) {
        $request['user_id'] = $user_id;
        $this->validate($request, [
            'user_id' => 'exists:users,id'
        ]);

        return handleResponseWithData(true,User::find($request->user_id)->profile);
    }

    public function saveProfile(Request $request) {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        $profile = UserProfile::where('user_id',auth()->user()->id)->firstOrCreate();
        $profile->update($request->all());
        
        return handleResponseWithData(true, $profile);
    }
}

