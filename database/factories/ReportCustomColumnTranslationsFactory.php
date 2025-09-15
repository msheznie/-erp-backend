<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ReportCustomColumnTranslations;
use Faker\Generator as Faker;

$factory->define(ReportCustomColumnTranslations::class, function (Faker $faker) {

    return [
        'documentSystemID' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
