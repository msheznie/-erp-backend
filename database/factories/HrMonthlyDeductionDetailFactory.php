<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HrMonthlyDeductionDetail;
use Faker\Generator as Faker;

$factory->define(HrMonthlyDeductionDetail::class, function (Faker $faker) {

    return [
        'monthlyDeductionMasterID' => $faker->randomDigitNotNull,
        'empID' => $faker->randomDigitNotNull,
        'accessGroupID' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'declarationID' => $faker->randomDigitNotNull,
        'GLCode' => $faker->randomDigitNotNull,
        'categoryID' => $faker->randomDigitNotNull,
        'transactionCurrencyID' => $faker->randomDigitNotNull,
        'transactionCurrency' => $faker->word,
        'transactionExchangeRate' => $faker->randomDigitNotNull,
        'transactionCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'transactionAmount' => $faker->randomDigitNotNull,
        'companyLocalCurrencyID' => $faker->randomDigitNotNull,
        'companyLocalCurrency' => $faker->word,
        'companyLocalExchangeRate' => $faker->randomDigitNotNull,
        'companyLocalAmount' => $faker->randomDigitNotNull,
        'companyLocalCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'companyReportingCurrencyID' => $faker->randomDigitNotNull,
        'companyReportingCurrency' => $faker->word,
        'companyReportingExchangeRate' => $faker->randomDigitNotNull,
        'companyReportingAmount' => $faker->randomDigitNotNull,
        'companyReportingCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'IsSSO' => $faker->randomDigitNotNull,
        'IsTax' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'segmentID' => $faker->randomDigitNotNull,
        'segmentCode' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
