<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RecurringVoucherSetupDetail;
use Faker\Generator as Faker;

$factory->define(RecurringVoucherSetupDetail::class, function (Faker $faker) {

    return [
        'recurringVoucherAutoId' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'chartOfAccountSystemID' => $faker->randomDigitNotNull,
        'currencyID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'glAccount' => $faker->word,
        'glAccountDescription' => $faker->text,
        'comments' => $faker->text,
        'debitAmount' => $faker->randomDigitNotNull,
        'creditAmount' => $faker->randomDigitNotNull,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'serviceLineCode' => $faker->word,
        'contractUID' => $faker->randomDigitNotNull,
        'clientContractID' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->randomDigitNotNull,
        'createdPcID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
