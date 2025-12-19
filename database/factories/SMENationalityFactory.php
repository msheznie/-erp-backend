<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMENationality;
use Faker\Generator as Faker;

$factory->define(SMENationality::class, function (Faker $faker) {

    return [
        'Nationality' => $faker->word,
        'NationalityAr' => $faker->word,
        'SchMasterID' => $faker->randomDigitNotNull,
        'BranchID' => $faker->randomDigitNotNull,
        'Erp_companyID' => $faker->randomDigitNotNull,
        'countryID' => $faker->randomDigitNotNull,
        'CreatedUserName' => $faker->word,
        'CreatedDate' => $faker->date('Y-m-d H:i:s'),
        'CreatedPC' => $faker->word,
        'ModifiedUserName' => $faker->word,
        'Timestamp' => $faker->date('Y-m-d H:i:s'),
        'ModifiedPC' => $faker->word
    ];
});
