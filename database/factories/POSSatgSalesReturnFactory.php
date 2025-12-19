<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSSatgSalesReturn;
use Faker\Generator as Faker;

$factory->define(POSSatgSalesReturn::class, function (Faker $faker) {

    return [
        'invoiceID' => $faker->randomDigitNotNull,
        'documentSystemCode' => $faker->word,
        'documentCode' => $faker->word,
        'serialNo' => $faker->randomDigitNotNull,
        'customerID' => $faker->randomDigitNotNull,
        'customerCode' => $faker->word,
        'counterID' => $faker->randomDigitNotNull,
        'shiftID' => $faker->randomDigitNotNull,
        'salesReturnDate' => $faker->word,
        'discountPer' => $faker->randomDigitNotNull,
        'discountAmount' => $faker->randomDigitNotNull,
        'generalDiscountPercentage' => $faker->randomDigitNotNull,
        'generalDiscountAmount' => $faker->randomDigitNotNull,
        'promotionID' => $faker->randomDigitNotNull,
        'promotiondiscount' => $faker->randomDigitNotNull,
        'promotiondiscountAmount' => $faker->randomDigitNotNull,
        'subTotal' => $faker->randomDigitNotNull,
        'netTotal' => $faker->randomDigitNotNull,
        'returnMode' => $faker->word,
        'isRefund' => $faker->word,
        'refundAmount' => $faker->randomDigitNotNull,
        'wareHouseAutoID' => $faker->randomDigitNotNull,
        'transactionCurrencyID' => $faker->randomDigitNotNull,
        'transactionCurrency' => $faker->word,
        'transactionExchangeRate' => $faker->randomDigitNotNull,
        'transactionCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'companyLocalCurrencyID' => $faker->randomDigitNotNull,
        'companyLocalCurrency' => $faker->word,
        'companyLocalExchangeRate' => $faker->randomDigitNotNull,
        'companyLocalCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'companyReportingCurrencyID' => $faker->randomDigitNotNull,
        'companyReportingCurrency' => $faker->word,
        'companyReportingExchangeRate' => $faker->randomDigitNotNull,
        'companyReportingCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'customerCurrencyID' => $faker->randomDigitNotNull,
        'customerCurrency' => $faker->word,
        'customerCurrencyExchangeRate' => $faker->randomDigitNotNull,
        'customerCurrencyAmount' => $faker->randomDigitNotNull,
        'customerCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'segmentID' => $faker->randomDigitNotNull,
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
        'customerReceivableAutoID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'isGroupBasedTax' => $faker->randomDigitNotNull,
        'transaction_log_id' => $faker->randomDigitNotNull
    ];
});
