<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VatReturnFillingCategoryLanguage;
use Faker\Generator as Faker;

$factory->define(VatReturnFillingCategoryLanguage::class, function (Faker $faker) {

    return [
        'returnFillingCategoryID' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
