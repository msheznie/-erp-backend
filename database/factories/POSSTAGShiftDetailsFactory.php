<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSSTAGShiftDetails;
use Faker\Generator as Faker;

$factory->define(POSSTAGShiftDetails::class, function (Faker $faker) {

    return [
        'cashSales' => $faker->randomDigitNotNull,
        'cashSales_local' => $faker->randomDigitNotNull,
        'cashSales_reporting' => $faker->randomDigitNotNull,
        'closingCashBalance_local' => $faker->randomDigitNotNull,
        'closingCashBalance_reporting' => $faker->randomDigitNotNull,
        'closingCashBalance_transaction' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'companyLocalCurrency' => $faker->word,
        'companyLocalCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'companyLocalCurrencyID' => $faker->randomDigitNotNull,
        'companyLocalExchangeRate' => $faker->randomDigitNotNull,
        'companyReportingCurrency' => $faker->word,
        'companyReportingCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'companyReportingCurrencyID' => $faker->randomDigitNotNull,
        'companyReportingExchangeRate' => $faker->randomDigitNotNull,
        'counterID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdPCID' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdUserName' => $faker->word,
        'different_local' => $faker->randomDigitNotNull,
        'different_local_reporting' => $faker->randomDigitNotNull,
        'different_transaction' => $faker->randomDigitNotNull,
        'empID' => $faker->randomDigitNotNull,
        'endingBalance_local' => $faker->randomDigitNotNull,
        'endingBalance_reporting' => $faker->randomDigitNotNull,
        'endingBalance_transaction' => $faker->randomDigitNotNull,
        'endTime' => $faker->date('Y-m-d H:i:s'),
        'giftCardTopUp' => $faker->randomDigitNotNull,
        'giftCardTopUp_local' => $faker->randomDigitNotNull,
        'giftCardTopUp_reporting' => $faker->randomDigitNotNull,
        'id_store' => $faker->randomDigitNotNull,
        'is_sync' => $faker->randomDigitNotNull,
        'isClosed' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedUserName' => $faker->word,
        'startingBalance_local' => $faker->randomDigitNotNull,
        'startingBalance_reporting' => $faker->randomDigitNotNull,
        'startingBalance_transaction' => $faker->randomDigitNotNull,
        'startTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'transaction_log_id' => $faker->randomDigitNotNull,
        'transactionCurrency' => $faker->word,
        'transactionCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'transactionCurrencyID' => $faker->randomDigitNotNull,
        'transactionExchangeRate' => $faker->randomDigitNotNull,
        'wareHouseID' => $faker->randomDigitNotNull
    ];
});
