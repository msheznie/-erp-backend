<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HrPayrollHeaderDetails;
use Faker\Generator as Faker;

$factory->define(HrPayrollHeaderDetails::class, function (Faker $faker) {

    return [
        'payrollMasterID' => $faker->randomDigitNotNull,
        'EmpID' => $faker->randomDigitNotNull,
        'accessGroupID' => $faker->randomDigitNotNull,
        'ECode' => $faker->word,
        'Ename1' => $faker->word,
        'Ename2' => $faker->word,
        'Ename3' => $faker->word,
        'Ename4' => $faker->word,
        'EmpShortCode' => $faker->word,
        'secondaryCode' => $faker->word,
        'Designation' => $faker->word,
        'Gender' => $faker->word,
        'Tel' => $faker->word,
        'Mobile' => $faker->word,
        'DOJ' => $faker->word,
        'payCurrencyID' => $faker->randomDigitNotNull,
        'payCurrency' => $faker->word,
        'nationality' => $faker->word,
        'totDayAbsent' => $faker->word,
        'totDayPresent' => $faker->word,
        'totOTHours' => $faker->word,
        'civilOrPassport' => $faker->word,
        'salaryArrearsDays' => $faker->word,
        'transactionCurrencyID' => $faker->randomDigitNotNull,
        'transactionCurrency' => $faker->word,
        'transactionER' => $faker->randomDigitNotNull,
        'transactionCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'transactionAmount' => $faker->randomDigitNotNull,
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
        'segmentID' => $faker->randomDigitNotNull,
        'segmentCode' => $faker->word,
        'payComment' => $faker->text,
        'companyID' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word
    ];
});
