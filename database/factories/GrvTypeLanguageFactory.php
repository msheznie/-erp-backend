<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\GrvTypeLanguage;
use Faker\Generator as Faker;

$factory->define(GrvTypeLanguage::class, function (Faker $faker) {

    return [
        'grvTypeID' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'des' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
