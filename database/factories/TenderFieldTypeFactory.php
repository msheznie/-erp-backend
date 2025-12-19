<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderFieldType;
use Faker\Generator as Faker;

$factory->define(TenderFieldType::class, function (Faker $faker) {

    return [
        'type' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
