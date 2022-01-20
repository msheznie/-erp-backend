<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ExpenseClaimDetailsMaster;
use Faker\Generator as Faker;

$factory->define(ExpenseClaimDetailsMaster::class, function (Faker $faker) {

    return [
        'expenseClaimMasterAutoID' => $faker->randomDigitNotNull,
        'expenseClaimCategoriesAutoID' => $faker->randomDigitNotNull,
        'crmDocumentID' => $faker->randomDigitNotNull,
        'crmDocumentDetailAutoID' => $faker->randomDigitNotNull,
        'description' => $faker->text,
        'referenceNo' => $faker->word,
        'segmentID' => $faker->randomDigitNotNull,
        'transactionCurrencyID' => $faker->randomDigitNotNull,
        'transactionCurrency' => $faker->word,
        'transactionExchangeRate' => $faker->randomDigitNotNull,
        'transactionAmount' => $faker->randomDigitNotNull,
        'transactionCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
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
        'empCurrencyID' => $faker->randomDigitNotNull,
        'empCurrency' => $faker->word,
        'empCurrencyExchangeRate' => $faker->randomDigitNotNull,
        'empCurrencyAmount' => $faker->randomDigitNotNull,
        'empCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'comments' => $faker->text,
        'companyID' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timeStamp' => $faker->date('Y-m-d H:i:s')
    ];
});
