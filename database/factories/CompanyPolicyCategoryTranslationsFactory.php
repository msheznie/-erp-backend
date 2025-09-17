<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CompanyPolicyCategoryTranslations;
use Faker\Generator as Faker;

$factory->define(CompanyPolicyCategoryTranslations::class, function (Faker $faker) {

    return [
        'companyPolicyCategoryID' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'comment' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
