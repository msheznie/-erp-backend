<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\LogisticShippingModeTranslations;
use Faker\Generator as Faker;

$factory->define(LogisticShippingModeTranslations::class, function (Faker $faker) {

    return [
        'logisticShippingModeID' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
