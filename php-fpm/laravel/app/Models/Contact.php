<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Psy\Exception\TypeErrorException;

class Contact extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'company_address',
        'company_location',
        'job_position',
        'phone',
        'mail',
        'photo'
    ];

    public function groups() {
        return $this->belongsToMany('App\Models\Group', 'contacts_groups');
    }

    public function share($attribute) { // array of groups id, integer of group_id, Group model
        if(gettype($attribute) == 'array') {
            foreach($attribute as $group_id) {
                $this->groups()->attach(Group::find($group_id));
            }
        } elseif (gettype($attribute) == 'integer') {
            $this->groups()->attach(Group::find($attribute));
        } elseif (get_class($attribute) == Group::class) {
            $this->groups()->attach($attribute);
        } else {
            throw new TypeErrorException("Type ".gettype($attribute)." is not valid.");
        }
    }
}
