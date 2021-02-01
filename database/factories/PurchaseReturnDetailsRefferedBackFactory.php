<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PurchaseReturnDetailsRefferedBack;
use Faker\Generator as Faker;

$factory->define(PurchaseReturnDetailsRefferedBack::class, function (Faker $faker) {

    return [
        'purhasereturnDetailID' => $faker->randomDigitNotNull,
        'purhaseReturnAutoID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'grvAutoID' => $faker->randomDigitNotNull,
        'grvDetailsID' => $faker->randomDigitNotNull,
        'itemCode' => $faker->randomDigitNotNull,
        'itemPrimaryCode' => $faker->word,
        'itemDescription' => $faker->text,
        'supplierPartNumber' => $faker->word,
        'unitOfMeasure' => $faker->randomDigitNotNull,
        'itemFinanceCategoryID' => $faker->randomDigitNotNull,
        'itemFinanceCategorySubID' => $faker->randomDigitNotNull,
        'financeGLcodebBSSystemID' => $faker->randomDigitNotNull,
        'financeGLcodebBS' => $faker->word,
        'financeGLcodePLSystemID' => $faker->randomDigitNotNull,
        'financeGLcodePL' => $faker->word,
        'includePLForGRVYN' => $faker->randomDigitNotNull,
        'GRVQty' => $faker->randomDigitNotNull,
        'comment' => $faker->text,
        'noQty' => $faker->randomDigitNotNull,
        'supplierDefaultCurrencyID' => $faker->randomDigitNotNull,
        'supplierDefaultER' => $faker->randomDigitNotNull,
        'supplierTransactionCurrencyID' => $faker->randomDigitNotNull,
        'supplierTransactionER' => $faker->randomDigitNotNull,
        'companyReportingCurrencyID' => $faker->randomDigitNotNull,
        'companyReportingER' => $faker->randomDigitNotNull,
        'localCurrencyID' => $faker->randomDigitNotNull,
        'localCurrencyER' => $faker->randomDigitNotNull,
        'GRVcostPerUnitLocalCur' => $faker->randomDigitNotNull,
        'GRVcostPerUnitSupDefaultCur' => $faker->randomDigitNotNull,
        'GRVcostPerUnitSupTransCur' => $faker->randomDigitNotNull,
        'GRVcostPerUnitComRptCur' => $faker->randomDigitNotNull,
        'netAmount' => $faker->randomDigitNotNull,
        'netAmountLocal' => $faker->randomDigitNotNull,
        'netAmountRpt' => $faker->randomDigitNotNull,
        'timeStamp' => $faker->date('Y-m-d H:i:s'),
        'GRVSelectedYN' => $faker->randomDigitNotNull,
        'goodsRecievedYN' => $faker->randomDigitNotNull,
        'receivedQty' => $faker->randomDigitNotNull
    ];
});
