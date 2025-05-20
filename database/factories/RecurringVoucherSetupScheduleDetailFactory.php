<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RecurringVoucherSetupScheduleDetail;
use Faker\Generator as Faker;

$factory->define(RecurringVoucherSetupScheduleDetail::class, function (Faker $faker) {

    return [
        'recurringVoucherAutoId' => $faker->randomDigitNotNull,
        'recurringVoucherSheduleAutoId' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'chartOfAccountSystemID' => $faker->randomDigitNotNull,
        'currencyID' => $faker->randomDigitNotNull,
        'detailProjectID' => $faker->randomDigitNotNull,
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
        'isChecked' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->randomDigitNotNull,
        'createdPcID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
