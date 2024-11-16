<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PaymentVoucherBankChargeDetails;
use Faker\Generator as Faker;

$factory->define(PaymentVoucherBankChargeDetails::class, function (Faker $faker) {

    return [
        'payMasterAutoID' => $faker->randomDigitNotNull,
        'companyID' => $faker->text,
        'companySystemID' => $faker->randomDigitNotNull,
        'chartOfAccountSystemID' => $faker->randomDigitNotNull,
        'glCode' => $faker->text,
        'glCodeDescription' => $faker->text,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'serviceLineCode' => $faker->text,
        'dpAmountCurrency' => $faker->randomDigitNotNull,
        'dpAmountCurrencyER' => $faker->text,
        'dpAmount' => $faker->randomDigitNotNull,
        'localCurrency' => $faker->randomDigitNotNull,
        'localCurrencyER' => $faker->text,
        'localAmount' => $faker->randomDigitNotNull,
        'comRptCurrency' => $faker->randomDigitNotNull,
        'comRptCurrencyER' => $faker->text,
        'comRptAmount' => $faker->randomDigitNotNull,
        'comment' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
