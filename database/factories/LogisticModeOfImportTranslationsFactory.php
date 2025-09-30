<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\LogisticModeOfImportTranslations;
use Faker\Generator as Faker;

$factory->define(LogisticModeOfImportTranslations::class, function (Faker $faker) {

    return [
        'modeOfImportID' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
