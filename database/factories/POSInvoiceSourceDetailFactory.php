<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSInvoiceSourceDetail;
use Faker\Generator as Faker;

$factory->define(POSInvoiceSourceDetail::class, function (Faker $faker) {

    return [
        'invoiceID' => $faker->randomDigitNotNull,
        'itemAutoID' => $faker->randomDigitNotNull,
        'itemCategory' => $faker->word,
        'financeCategory' => $faker->randomDigitNotNull,
        'itemFinanceCategory' => $faker->randomDigitNotNull,
        'itemFinanceCategorySub' => $faker->randomDigitNotNull,
        'defaultUOMID' => $faker->randomDigitNotNull,
        'UOMID' => $faker->randomDigitNotNull,
        'unitOfMeasure' => $faker->word,
        'conversionRateUOM' => $faker->randomDigitNotNull,
        'expenseGLAutoID' => $faker->randomDigitNotNull,
        'revenueGLAutoID' => $faker->randomDigitNotNull,
        'assetGLAutoID' => $faker->randomDigitNotNull,
        'qty' => $faker->randomDigitNotNull,
        'price' => $faker->randomDigitNotNull,
        'discountPer' => $faker->randomDigitNotNull,
        'discountAmount' => $faker->randomDigitNotNull,
        'generalDiscountPercentage' => $faker->randomDigitNotNull,
        'generalDiscountAmount' => $faker->randomDigitNotNull,
        'promoID' => $faker->randomDigitNotNull,
        'promotiondiscount' => $faker->randomDigitNotNull,
        'promotiondiscountAmount' => $faker->randomDigitNotNull,
        'taxCalculationformulaID' => $faker->randomDigitNotNull,
        'taxAmount' => $faker->randomDigitNotNull,
        'transactionCurrencyID' => $faker->randomDigitNotNull,
        'transactionCurrency' => $faker->word,
        'transactionAmountBeforeDiscount' => $faker->randomDigitNotNull,
        'transactionAmount' => $faker->randomDigitNotNull,
        'transactionCurrencyDecimalPlaces' => $faker->word,
        'transactionExchangeRate' => $faker->randomDigitNotNull,
        'companyLocalCurrencyID' => $faker->randomDigitNotNull,
        'companyLocalCurrency' => $faker->word,
        'companyLocalAmount' => $faker->randomDigitNotNull,
        'companyLocalExchangeRate' => $faker->randomDigitNotNull,
        'companyLocalCurrencyDecimalPlaces' => $faker->word,
        'companyReportingCurrencyID' => $faker->randomDigitNotNull,
        'companyReportingCurrency' => $faker->word,
        'companyReportingAmount' => $faker->randomDigitNotNull,
        'companyReportingCurrencyDecimalPlaces' => $faker->word,
        'companyReportingExchangeRate' => $faker->randomDigitNotNull,
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
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'transaction_log_id' => $faker->randomDigitNotNull,
        'mapping_master_id' => $faker->randomDigitNotNull
    ];
});
