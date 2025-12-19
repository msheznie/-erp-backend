<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EvaluationType;
use Faker\Generator as Faker;

$factory->define(EvaluationType::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s')
    ];
});
