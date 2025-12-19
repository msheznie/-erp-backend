<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HREmpContractHistory;
use Faker\Generator as Faker;

$factory->define(HREmpContractHistory::class, function (Faker $faker) {

    return [
        'empID' => $faker->randomDigitNotNull,
        'contactTypeID' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'contractStartDate' => $faker->word,
        'contractEndDate' => $faker->word,
        'contractRefNo' => $faker->word,
        'isCurrent' => $faker->randomDigitNotNull,
        'previousContractID' => $faker->randomDigitNotNull,
        'CreatedUserName' => $faker->word,
        'CreatedDate' => $faker->date('Y-m-d H:i:s'),
        'CreatedPC' => $faker->word,
        'ModifiedUserName' => $faker->word,
        'ModifiedPC' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
