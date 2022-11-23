<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ReasonCodeMaster;
use Faker\Generator as Faker;

$factory->define(ReasonCodeMaster::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'isPost' => $faker->word,
        'glCode' => $faker->word
    ];
});
