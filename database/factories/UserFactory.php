<?php

use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => '123456',
        'remember_token' => Str::random(10),
    ];
});

$factory->state(User::class, 'with_plain_password', function (Faker $faker) {
    return [
        'first_name' 			=> $faker->firstName,
        'last_name' 			=> $faker->lastName,
        'email' 				=> $faker->unique()->safeEmail,
        'password' 				=> '123456',
        'password_confirmation' => '123456',
        'remember_token' 		=> Str::random(10),
    ];
});
