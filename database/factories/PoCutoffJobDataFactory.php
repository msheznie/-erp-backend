<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PoCutoffJobData;
use Faker\Generator as Faker;

$factory->define(PoCutoffJobData::class, function (Faker $faker) {

    return [
        'documentCode' => $faker->word,
        'segment' => $faker->word,
        'currency' => $faker->word,
        'documentValue' => $faker->word,
        'remainingValue' => $faker->word,
        'cutOffDate' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
