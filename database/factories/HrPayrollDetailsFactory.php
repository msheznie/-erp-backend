<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HrPayrollDetails;
use Faker\Generator as Faker;

$factory->define(HrPayrollDetails::class, function (Faker $faker) {

    return [
        'payrollMasterID' => $faker->randomDigitNotNull,
        'empID' => $faker->randomDigitNotNull,
        'detailTBID' => $faker->randomDigitNotNull,
        'fromTB' => $faker->word,
        'calculationTB' => $faker->word,
        'detailType' => $faker->word,
        'salCatID' => $faker->randomDigitNotNull,
        'percentage' => $faker->randomDigitNotNull,
        'GLCode' => $faker->randomDigitNotNull,
        'liabilityGL' => $faker->randomDigitNotNull,
        'transactionCurrencyID' => $faker->randomDigitNotNull,
        'transactionCurrency' => $faker->word,
        'transactionER' => $faker->randomDigitNotNull,
        'transactionCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'transactionAmount' => $faker->randomDigitNotNull,
        'pasiActualAmount' => $faker->randomDigitNotNull,
        'companyLocalCurrencyID' => $faker->randomDigitNotNull,
        'companyLocalCurrency' => $faker->word,
        'companyLocalER' => $faker->randomDigitNotNull,
        'companyLocalCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'companyLocalAmount' => $faker->randomDigitNotNull,
        'companyReportingCurrencyID' => $faker->randomDigitNotNull,
        'companyReportingCurrency' => $faker->word,
        'companyReportingER' => $faker->randomDigitNotNull,
        'companyReportingCurrencyDecimalPlaces' => $faker->word,
        'companyReportingAmount' => $faker->randomDigitNotNull,
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
