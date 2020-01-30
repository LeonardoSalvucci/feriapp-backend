<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Contact;
use Faker\Generator as Faker;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'first_name'=>$faker->firstName,
        'last_name'=>$faker->lastName,
        'company_name'=>$faker->company,
        'company_address'=>$faker->address,
        'company_location'=>$faker->city,
        'job_position'=>$faker->jobTitle,
        'phone'=>$faker->phoneNumber,
        'mail'=>$faker->email,
        //'photo'=>$faker->image(storage_path('public/images'), 400, 300)
    ];
});
