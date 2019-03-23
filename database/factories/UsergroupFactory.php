<?php

use Faker\Generator as Faker;

$factory->define(App\Usergroup::class, function (Faker $faker) {
    return [
        'name'      => 'test_user_group',
        'accesses'  => null,
        'user_id'   => 1,
    ];
});

$factory->state(App\Usergroup::class, 'admin', function (Faker $faker) {
    return [
            'accesses' => '{
            "users": {
                "create": 1,
                "update": "all",
                "delete": "all",
                "access": 1,
                "manage": 1
            },
            "usergroups": {
                "create": 1,
                "update": "all",
                "delete": "all",
                "access": 1,
                "manage": 1
            },
            "logs": {
                "create": 1,
                "update": "all",
                "delete": "all",
                "access": 1,
                "manage": 1
            },
            "teams": {
                "create": 1,
                "update": "all",
                "delete": "all",
                "access": 1,
                "manage": 1
            }
        }',
    ];
});

$factory->state(App\Usergroup::class, 'without_permissions', function (Faker $faker) {
    return [
        'accesses' => null,
    ];
});
