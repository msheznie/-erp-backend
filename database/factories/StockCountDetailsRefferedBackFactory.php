<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\StockCountDetailsRefferedBack;
use Faker\Generator as Faker;

$factory->define(StockCountDetailsRefferedBack::class, function (Faker $faker) {

    return [
        'stockCountDetailsAutoID' => $faker->randomDigitNotNull,
        'stockCountAutoID' => $faker->randomDigitNotNull,
        'stockCountAutoIDCode' => $faker->word,
        'itemCodeSystem' => $faker->randomDigitNotNull,
        'itemPrimaryCode' => $faker->word,
        'itemDescription' => $faker->text,
        'itemUnitOfMeasure' => $faker->randomDigitNotNull,
        'partNumber' => $faker->word,
        'itemFinanceCategoryID' => $faker->randomDigitNotNull,
        'itemFinanceCategorySubID' => $faker->randomDigitNotNull,
        'financeGLcodebBSSystemID' => $faker->randomDigitNotNull,
        'financeGLcodebBS' => $faker->word,
        'financeGLcodePLSystemID' => $faker->randomDigitNotNull,
        'financeGLcodePL' => $faker->word,
        'includePLForGRVYN' => $faker->randomDigitNotNull,
        'systemQty' => $faker->randomDigitNotNull,
        'noQty' => $faker->randomDigitNotNull,
        'adjustedQty' => $faker->randomDigitNotNull,
        'comments' => $faker->text,
        'currentWacLocalCurrencyID' => $faker->randomDigitNotNull,
        'currentWaclocal' => $faker->randomDigitNotNull,
        'currentWacRptCurrencyID' => $faker->randomDigitNotNull,
        'currentWacRpt' => $faker->randomDigitNotNull,
        'wacAdjLocal' => $faker->randomDigitNotNull,
        'wacAdjRptER' => $faker->randomDigitNotNull,
        'wacAdjRpt' => $faker->randomDigitNotNull,
        'wacAdjLocalER' => $faker->randomDigitNotNull,
        'currenctStockQty' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'updatedFlag' => $faker->word
    ];
});
