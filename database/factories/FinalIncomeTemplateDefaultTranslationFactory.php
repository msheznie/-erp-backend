<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FinalIncomeTemplateDefaultTranslation;
use Faker\Generator as Faker;

$factory->define(FinalIncomeTemplateDefaultTranslation::class, function (Faker $faker) {

    return [
        'defaultId' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
