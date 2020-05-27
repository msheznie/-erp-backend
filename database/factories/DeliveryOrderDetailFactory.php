<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DeliveryOrderDetail;
use Faker\Generator as Faker;

$factory->define(DeliveryOrderDetail::class, function (Faker $faker) {

    return [
        'deliveryOrderID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'itemCodeSystem' => $faker->randomDigitNotNull,
        'itemPrimaryCode' => $faker->word,
        'itemDescription' => $faker->text,
        'itemUnitOfMeasure' => $faker->randomDigitNotNull,
        'unitOfMeasureIssued' => $faker->randomDigitNotNull,
        'convertionMeasureVal' => $faker->randomDigitNotNull,
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
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
