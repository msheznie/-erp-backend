<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PdcLog;
use Faker\Generator as Faker;

$factory->define(PdcLog::class, function (Faker $faker) {

    return [
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentmasterAutoID' => $faker->randomDigitNotNull,
        'paymentBankID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'currencyID' => $faker->randomDigitNotNull,
        'chequeRegisterAutoID' => $faker->randomDigitNotNull,
        'chequeNo' => $faker->word,
        'chequeDate' => $faker->date('Y-m-d H:i:s'),
        'chequeStatus' => $faker->randomDigitNotNull,
        'amount' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
