<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RecurringVoucherSetupSchedule;
use Faker\Generator as Faker;

$factory->define(RecurringVoucherSetupSchedule::class, function (Faker $faker) {

    return [
        'recurringVoucherAutoId' => $faker->randomDigitNotNull,
        'processDate' => $faker->date('Y-m-d H:i:s'),
        'RRVcode' => $faker->word,
        'currencyID' => $faker->randomDigitNotNull,
        'amount' => $faker->randomDigitNotNull,
        'jvGeneratedYN' => $faker->randomDigitNotNull,
        'stopYN' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyFinanceYearID' => $faker->randomDigitNotNull,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->randomDigitNotNull,
        'createdPcID' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'modifiedUser' => $faker->word,
        'modifiedPc' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
