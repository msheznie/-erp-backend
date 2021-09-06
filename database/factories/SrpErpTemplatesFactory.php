<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SrpErpTemplates;
use Faker\Generator as Faker;

$factory->define(SrpErpTemplates::class, function (Faker $faker) {

    return [
        'companyID' => $faker->randomDigitNotNull,
        'TempMasterID' => $faker->randomDigitNotNull,
        'FormCatID' => $faker->randomDigitNotNull,
        'navigationMenuID' => $faker->randomDigitNotNull,
        'templateKey' => $faker->word,
        'CreatedUserName' => $faker->word,
        'CreatedDate' => $faker->date('Y-m-d H:i:s'),
        'CreatedPC' => $faker->word,
        'ModifiedUserName' => $faker->word,
        'Timestamp' => $faker->date('Y-m-d H:i:s'),
        'ModifiedPC' => $faker->word
    ];
});
