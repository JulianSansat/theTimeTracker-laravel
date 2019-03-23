<?php

use App\Team;
use Illuminate\Support\Str;
use Faker\Generator as Faker;


$factory->define(Team::class, function (Faker $faker) {
    return [
        'name'          => $faker->streetName,
        'description'	=> $faker->text,
        'logo_origin'   => 1, //remote img origin
        'logo_path' 	=> $faker->imageUrl($width = 640, $height = 480),
        'description'   => $faker->text($maxNbChars = 200),
        'author_id'     => 1
    ];
});