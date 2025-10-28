<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ExpensesClaimTypeLanguage;
use Faker\Generator as Faker;

$factory->define(ExpensesClaimTypeLanguage::class, function (Faker $faker) {

    return [
        'typeId' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
