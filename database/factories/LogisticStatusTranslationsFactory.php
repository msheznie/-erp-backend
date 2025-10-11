<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\LogisticStatusTranslations;
use Faker\Generator as Faker;

$factory->define(LogisticStatusTranslations::class, function (Faker $faker) {

    return [
        'StatusID' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
