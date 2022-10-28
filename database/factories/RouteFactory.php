<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Route;
use Faker\Generator as Faker;

$factory->define(Route::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'method' => $faker->word,
        'action' => $faker->word,
        'uri' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
