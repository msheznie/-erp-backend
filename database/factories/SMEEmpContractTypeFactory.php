<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMEEmpContractType;
use Faker\Generator as Faker;

$factory->define(SMEEmpContractType::class, function (Faker $faker) {

    return [
        'Description' => $faker->word,
        'typeID' => $faker->randomDigitNotNull,
        'probation_period' => $faker->randomDigitNotNull,
        'period' => $faker->randomDigitNotNull,
        'is_open_contract' => $faker->randomDigitNotNull,
        'SchMasterID' => $faker->randomDigitNotNull,
        'BranchID' => $faker->randomDigitNotNull,
        'Erp_CompanyID' => $faker->randomDigitNotNull,
        'CreatedUserName' => $faker->word,
        'CreatedDate' => $faker->date('Y-m-d H:i:s'),
        'CreatedPC' => $faker->word,
        'ModifiedUserName' => $faker->word,
        'Timestamp' => $faker->date('Y-m-d H:i:s'),
        'ModifiedPC' => $faker->word
    ];
});
