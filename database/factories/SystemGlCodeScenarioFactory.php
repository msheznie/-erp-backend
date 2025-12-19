<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SystemGlCodeScenario;
use Faker\Generator as Faker;

$factory->define(SystemGlCodeScenario::class, function (Faker $faker) {

    return [
        'documentSystemID' => $faker->randomDigitNotNull,
        'description' => $faker->word
    ];
});
