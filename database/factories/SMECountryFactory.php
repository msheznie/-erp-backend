<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMECountry;
use Faker\Generator as Faker;

$factory->define(SMECountry::class, function (Faker $faker) {

    return [
        'countryShortCode' => $faker->word,
        'CountryDes' => $faker->word,
        'CountryTelCode' => $faker->word,
        'countryMasterID' => $faker->randomDigitNotNull,
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
