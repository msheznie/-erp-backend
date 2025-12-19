<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SupplierInvoiceItemDetail;
use Faker\Generator as Faker;

$factory->define(SupplierInvoiceItemDetail::class, function (Faker $faker) {

    return [
        'bookingSupInvoiceDetAutoID' => $faker->randomDigitNotNull,
        'bookingSuppMasInvAutoID' => $faker->randomDigitNotNull,
        'unbilledgrvAutoID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'grvDetailsID' => $faker->randomDigitNotNull,
        'purchaseOrderID' => $faker->randomDigitNotNull,
        'grvAutoID' => $faker->randomDigitNotNull,
        'supplierTransactionCurrencyID' => $faker->randomDigitNotNull,
        'supplierTransactionCurrencyER' => $faker->randomDigitNotNull,
        'companyReportingCurrencyID' => $faker->randomDigitNotNull,
        'companyReportingER' => $faker->randomDigitNotNull,
        'localCurrencyID' => $faker->randomDigitNotNull,
        'localCurrencyER' => $faker->randomDigitNotNull,
        'supplierInvoOrderedAmount' => $faker->randomDigitNotNull,
        'supplierInvoAmount' => $faker->randomDigitNotNull,
        'transSupplierInvoAmount' => $faker->randomDigitNotNull,
        'localSupplierInvoAmount' => $faker->randomDigitNotNull,
        'rptSupplierInvoAmount' => $faker->randomDigitNotNull,
        'totTransactionAmount' => $faker->randomDigitNotNull,
        'totLocalAmount' => $faker->randomDigitNotNull,
        'totRptAmount' => $faker->randomDigitNotNull,
        'VATAmount' => $faker->randomDigitNotNull,
        'VATAmountLocal' => $faker->randomDigitNotNull,
        'VATAmountRpt' => $faker->randomDigitNotNull,
        'timeStamp' => $faker->date('Y-m-d H:i:s')
    ];
});
