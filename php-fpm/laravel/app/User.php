<?php

namespace App;

use App\Models\Group;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile() {
        return $this->hasOne('App\Models\UserProfile')->withDefault([
            'photo' => '0.jpeg',
            'first_name' => null,
            'last_name' => null
        ]);
    }

    public function groups() {
        return $this->belongsToMany('App\Models\Group','groups_users');
    }

    /**
     * Create a group with user as owner and attach.
     *
     * @return \App\Models\Group
     */
    public function addGroup(array $attributes) {
        $group = Group::create(array_merge($attributes,['user_owner_id'=>$this->id]));
        $this->groups()->attach($group);
        return $group;
    }

    /**
     * The user has the group.
     *
     * @return boolean
     */
    public function hasGroup(Group $group) {
        return $this->groups->contains($group);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
