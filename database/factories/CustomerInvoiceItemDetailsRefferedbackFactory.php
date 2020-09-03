<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomerInvoiceItemDetailsRefferedback;
use Faker\Generator as Faker;

$factory->define(CustomerInvoiceItemDetailsRefferedback::class, function (Faker $faker) {

    return [
        'customerItemDetailID' => $faker->randomDigitNotNull,
        'custInvoiceDirectAutoID' => $faker->randomDigitNotNull,
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
        'comments' => $faker->text,
        'itemFinanceCategoryID' => $faker->randomDigitNotNull,
        'itemFinanceCategorySubID' => $faker->randomDigitNotNull,
        'financeGLcodebBSSystemID' => $faker->randomDigitNotNull,
        'financeGLcodebBS' => $faker->word,
        'financeGLcodePLSystemID' => $faker->randomDigitNotNull,
        'financeGLcodePL' => $faker->word,
        'financeGLcodeRevenueSystemID' => $faker->randomDigitNotNull,
        'financeGLcodeRevenue' => $faker->word,
        'includePLForGRVYN' => $faker->randomDigitNotNull,
        'localCurrencyID' => $faker->randomDigitNotNull,
        'localCurrencyER' => $faker->randomDigitNotNull,
        'issueCostLocal' => $faker->randomDigitNotNull,
        'issueCostLocalTotal' => $faker->randomDigitNotNull,
        'reportingCurrencyID' => $faker->randomDigitNotNull,
        'reportingCurrencyER' => $faker->randomDigitNotNull,
        'issueCostRpt' => $faker->randomDigitNotNull,
        'issueCostRptTotal' => $faker->randomDigitNotNull,
        'marginPercentage' => $faker->randomDigitNotNull,
        'sellingCurrencyID' => $faker->randomDigitNotNull,
        'sellingCurrencyER' => $faker->randomDigitNotNull,
        'sellingCost' => $faker->randomDigitNotNull,
        'sellingCostAfterMargin' => $faker->randomDigitNotNull,
        'sellingTotal' => $faker->randomDigitNotNull,
        'sellingCostAfterMarginLocal' => $faker->randomDigitNotNull,
        'sellingCostAfterMarginRpt' => $faker->randomDigitNotNull,
        'customerCatalogDetailID' => $faker->randomDigitNotNull,
        'customerCatalogMasterID' => $faker->randomDigitNotNull,
        'deliveryOrderDetailID' => $faker->randomDigitNotNull,
        'deliveryOrderID' => $faker->randomDigitNotNull,
        'quotationMasterID' => $faker->randomDigitNotNull,
        'quotationDetailsID' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
