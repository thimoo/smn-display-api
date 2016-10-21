<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Display::class, function (Faker\Generator $faker) {
    return [
        'data'       => $faker->boolean,
        'collection' => $faker->boolean,
    ];
});

$factory->define(App\Value::class, function (Faker\Generator $faker) {
    return [
        'value' => $faker->randomFloat(2, -10, 100),
        'tag'   => $faker->randomElement(['original', 'smoothed', 'substituted', 'no-data']),
    ];
});
