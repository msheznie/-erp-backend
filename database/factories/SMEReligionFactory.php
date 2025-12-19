<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMEReligion;
use Faker\Generator as Faker;

$factory->define(SMEReligion::class, function (Faker $faker) {

    return [
        'Religion' => $faker->word,
        'ReligionAr' => $faker->word,
        'SchMasterID' => $faker->randomDigitNotNull,
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
