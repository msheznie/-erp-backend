<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PurchaseReturnLogistic;
use Faker\Generator as Faker;

$factory->define(PurchaseReturnLogistic::class, function (Faker $faker) {

    return [
        'grvAutoID' => $faker->randomDigitNotNull,
        'grvDetailID' => $faker->randomDigitNotNull,
        'purchaseReturnID' => $faker->randomDigitNotNull,
        'purchaseReturnDetailID' => $faker->randomDigitNotNull,
        'logisticAmountTrans' => $faker->randomDigitNotNull,
        'logisticAmountRpt' => $faker->randomDigitNotNull,
        'logisticAmountLocal' => $faker->randomDigitNotNull,
        'logisticVATAmount' => $faker->randomDigitNotNull,
        'logisticVATAmountLocal' => $faker->randomDigitNotNull,
        'logisticVATAmountRpt' => $faker->randomDigitNotNull,
        'UnbilledGRVAccountSystemID' => $faker->randomDigitNotNull,
        'supplierID' => $faker->randomDigitNotNull,
        'supplierTransactionCurrencyID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
