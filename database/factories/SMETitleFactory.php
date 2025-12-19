<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMETitle;
use Faker\Generator as Faker;

$factory->define(SMETitle::class, function (Faker $faker) {

    return [
        'TitleDescription' => $faker->word,
        'SchMasterId' => $faker->randomDigitNotNull,
        'BranchID' => $faker->randomDigitNotNull,
        'Erp_companyID' => $faker->randomDigitNotNull,
        'CreatedUserName' => $faker->word,
        'CreatedDate' => $faker->date('Y-m-d H:i:s'),
        'CreatedPC' => $faker->word,
        'ModifiedUserName' => $faker->word,
        'Timestamp' => $faker->date('Y-m-d H:i:s'),
        'ModifiedPC' => $faker->word
    ];
});
