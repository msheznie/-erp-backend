<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CompanyPolicyMasterTranslations;
use Faker\Generator as Faker;

$factory->define(CompanyPolicyMasterTranslations::class, function (Faker $faker) {

    return [
        'companypolicymasterID' => $faker->word,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
