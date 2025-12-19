<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderType;
use Faker\Generator as Faker;

$factory->define(TenderType::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s')
    ];
});
