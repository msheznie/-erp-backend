<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EmployeeLedger;
use Faker\Generator as Faker;

$factory->define(EmployeeLedger::class, function (Faker $faker) {

    return [
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'documentCode' => $faker->word,
        'documentDate' => $faker->date('Y-m-d H:i:s'),
        'employeeSystemID' => $faker->randomDigitNotNull,
        'supplierInvoiceNo' => $faker->word,
        'supplierInvoiceDate' => $faker->date('Y-m-d H:i:s'),
        'supplierTransCurrencyID' => $faker->randomDigitNotNull,
        'supplierTransER' => $faker->randomDigitNotNull,
        'supplierInvoiceAmount' => $faker->randomDigitNotNull,
        'supplierDefaultCurrencyID' => $faker->randomDigitNotNull,
        'supplierDefaultCurrencyER' => $faker->randomDigitNotNull,
        'supplierDefaultAmount' => $faker->randomDigitNotNull,
        'localCurrencyID' => $faker->randomDigitNotNull,
        'localER' => $faker->randomDigitNotNull,
        'localAmount' => $faker->randomDigitNotNull,
        'comRptCurrencyID' => $faker->randomDigitNotNull,
        'comRptER' => $faker->randomDigitNotNull,
        'comRptAmount' => $faker->randomDigitNotNull,
        'isInvoiceLockedYN' => $faker->randomDigitNotNull,
        'lockedBy' => $faker->word,
        'lockedByEmpName' => $faker->word,
        'lockedDate' => $faker->date('Y-m-d H:i:s'),
        'lockedComments' => $faker->text,
        'invoiceType' => $faker->randomDigitNotNull,
        'selectedToPaymentInv' => $faker->randomDigitNotNull,
        'fullyInvoice' => $faker->randomDigitNotNull,
        'advancePaymentTypeID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdPcID' => $faker->word,
        'timeStamp' => $faker->date('Y-m-d H:i:s'),
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
