<?php

use App\Log;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Log::class, function (Faker $faker) {
    $hours = $faker->numberBetween($min = 1, $max = 12);
    $minutes = $faker->numberBetween($min = 0, $max = 60);

    $now = date("Y-m-d H:i:s");

    $finish = date('Y-m-d H:i:s',strtotime('+'.$hours.' hour +'.$minutes.' minutes',strtotime($now)));
    return [
        'start'     => $now,
        'finish'    => $finish,
        'user_id'   => 1
    ];
});
