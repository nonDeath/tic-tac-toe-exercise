<?php

use Faker\Generator as Faker;

$factory->define(\App\Match::class, function (Faker $faker) {
    return [
        'name' => 'Match '. $faker->numerify('#'),
        'next' => rand(1, 2),
        'winner' => 0,
        'board' => [
            0, 0, 0,
            0, 0, 0,
            0, 0, 0,
        ]
    ];
});
