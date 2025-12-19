<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SupplierInvoiceDirectItem;
use Faker\Generator as Faker;

$factory->define(SupplierInvoiceDirectItem::class, function (Faker $faker) {

    return [
        'bookingSuppMasInvAutoID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'itemCode' => $faker->randomDigitNotNull,
        'itemPrimaryCode' => $faker->word,
        'itemDescription' => $faker->text,
        'itemFinanceCategoryID' => $faker->randomDigitNotNull,
        'itemFinanceCategorySubID' => $faker->randomDigitNotNull,
        'financeGLcodebBSSystemID' => $faker->randomDigitNotNull,
        'financeGLcodePLSystemID' => $faker->randomDigitNotNull,
        'includePLForGRVYN' => $faker->randomDigitNotNull,
        'supplierPartNumber' => $faker->word,
        'unitOfMeasure' => $faker->randomDigitNotNull,
        'trackingType' => $faker->randomDigitNotNull,
        'noQty' => $faker->randomDigitNotNull,
        'unitCost' => $faker->randomDigitNotNull,
        'netAmount' => $faker->randomDigitNotNull,
        'comment' => $faker->text,
        'supplierDefaultCurrencyID' => $faker->randomDigitNotNull,
        'supplierDefaultER' => $faker->randomDigitNotNull,
        'supplierItemCurrencyID' => $faker->randomDigitNotNull,
        'foreignToLocalER' => $faker->randomDigitNotNull,
        'companyReportingCurrencyID' => $faker->randomDigitNotNull,
        'companyReportingER' => $faker->randomDigitNotNull,
        'localCurrencyID' => $faker->randomDigitNotNull,
        'localCurrencyER' => $faker->randomDigitNotNull,
        'costPerUnitLocalCur' => $faker->randomDigitNotNull,
        'costPerUnitSupDefaultCur' => $faker->randomDigitNotNull,
        'costPerUnitSupTransCur' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'createdPcID' => $faker->word,
        'createdUserID' => $faker->randomDigitNotNull,
        'modifiedPc' => $faker->word,
        'modifiedUser' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
