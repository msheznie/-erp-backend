<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AdvanceReceiptDetails;
use Faker\Generator as Faker;

$factory->define(AdvanceReceiptDetails::class, function (Faker $faker) {

    return [
        'custReceivePaymentAutoID' => $faker->randomDigitNotNull,
        'soAdvPaymentID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'salesOrderID' => $faker->randomDigitNotNull,
        'salesOrderCode' => $faker->word,
        'comments' => $faker->text,
        'paymentAmount' => $faker->randomDigitNotNull,
        'customerTransCurrencyID' => $faker->randomDigitNotNull,
        'customerTransER' => $faker->randomDigitNotNull,
        'customerDefaultCurrencyID' => $faker->randomDigitNotNull,
        'customerDefaultCurrencyER' => $faker->randomDigitNotNull,
        'localCurrencyID' => $faker->randomDigitNotNull,
        'localER' => $faker->randomDigitNotNull,
        'comRptCurrencyID' => $faker->randomDigitNotNull,
        'comRptER' => $faker->randomDigitNotNull,
        'supplierDefaultAmount' => $faker->randomDigitNotNull,
        'supplierTransAmount' => $faker->randomDigitNotNull,
        'localAmount' => $faker->randomDigitNotNull,
        'comRptAmount' => $faker->randomDigitNotNull,
        'VATAmount' => $faker->randomDigitNotNull,
        'VATAmountLocal' => $faker->randomDigitNotNull,
        'VATAmountRpt' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'timeStamp' => $faker->date('Y-m-d H:i:s')
    ];
});
