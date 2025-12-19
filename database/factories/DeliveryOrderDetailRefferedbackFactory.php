<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DeliveryOrderDetailRefferedback;
use Faker\Generator as Faker;

$factory->define(DeliveryOrderDetailRefferedback::class, function (Faker $faker) {

    return [
        'deliveryOrderDetailID' => $faker->randomDigitNotNull,
        'deliveryOrderID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'itemCodeSystem' => $faker->randomDigitNotNull,
        'itemPrimaryCode' => $faker->word,
        'itemDescription' => $faker->text,
        'itemUnitOfMeasure' => $faker->randomDigitNotNull,
        'unitOfMeasureIssued' => $faker->randomDigitNotNull,
        'convertionMeasureVal' => $faker->randomDigitNotNull,
        'itemFinanceCategoryID' => $faker->randomDigitNotNull,
        'itemFinanceCategorySubID' => $faker->word,
        'financeGLcodebBSSystemID' => $faker->randomDigitNotNull,
        'financeGLcodebBS' => $faker->word,
        'financeGLcodePLSystemID' => $faker->randomDigitNotNull,
        'financeGLcodePL' => $faker->word,
        'financeGLcodeRevenueSystemID' => $faker->randomDigitNotNull,
        'financeGLcodeRevenue' => $faker->word,
        'qtyIssued' => $faker->randomDigitNotNull,
        'qtyIssuedDefaultMeasure' => $faker->randomDigitNotNull,
        'currentStockQty' => $faker->randomDigitNotNull,
        'currentWareHouseStockQty' => $faker->randomDigitNotNull,
        'currentStockQtyInDamageReturn' => $faker->randomDigitNotNull,
        'wacValueLocal' => $faker->randomDigitNotNull,
        'wacValueReporting' => $faker->randomDigitNotNull,
        'unitTransactionAmount' => $faker->randomDigitNotNull,
        'discountPercentage' => $faker->randomDigitNotNull,
        'discountAmount' => $faker->randomDigitNotNull,
        'transactionCurrencyID' => $faker->randomDigitNotNull,
        'transactionCurrencyER' => $faker->randomDigitNotNull,
        'transactionAmount' => $faker->randomDigitNotNull,
        'companyLocalCurrencyID' => $faker->randomDigitNotNull,
        'companyLocalCurrencyER' => $faker->randomDigitNotNull,
        'companyLocalAmount' => $faker->randomDigitNotNull,
        'companyReportingCurrencyID' => $faker->randomDigitNotNull,
        'companyReportingCurrencyER' => $faker->randomDigitNotNull,
        'companyReportingAmount' => $faker->randomDigitNotNull,
        'quotationMasterID' => $faker->randomDigitNotNull,
        'quotationDetailsID' => $faker->randomDigitNotNull,
        'remarks' => $faker->text,
        'requestedQty' => $faker->randomDigitNotNull,
        'balanceQty' => $faker->randomDigitNotNull,
        'fullyReceived' => $faker->randomDigitNotNull,
        'invQty' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
