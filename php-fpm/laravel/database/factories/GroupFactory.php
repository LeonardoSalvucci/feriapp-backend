<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'name' => $faker->title,
        'location' => $faker->city,
        //'photo' => $faker->image(storage_path('public/images'),400,300)
    ];
});
