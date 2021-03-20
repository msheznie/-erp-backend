<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SalesOrderAdvPayment;
use Faker\Generator as Faker;

$factory->define(SalesOrderAdvPayment::class, function (Faker $faker) {

    return [
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'serviceLineID' => $faker->word,
        'soID' => $faker->randomDigitNotNull,
        'grvAutoID' => $faker->randomDigitNotNull,
        'soCode' => $faker->word,
        'soTermID' => $faker->randomDigitNotNull,
        'supplierID' => $faker->randomDigitNotNull,
        'SupplierPrimaryCode' => $faker->word,
        'liabilityAccountSysemID' => $faker->randomDigitNotNull,
        'liabilityAccount' => $faker->word,
        'UnbilledGRVAccountSystemID' => $faker->randomDigitNotNull,
        'UnbilledGRVAccount' => $faker->word,
        'reqDate' => $faker->date('Y-m-d H:i:s'),
        'narration' => $faker->text,
        'currencyID' => $faker->randomDigitNotNull,
        'reqAmount' => $faker->randomDigitNotNull,
        'reqAmountTransCur_amount' => $faker->randomDigitNotNull,
        'logisticCategoryID' => $faker->randomDigitNotNull,
        'confirmedYN' => $faker->randomDigitNotNull,
        'approvedYN' => $faker->randomDigitNotNull,
        'selectedToPayment' => $faker->randomDigitNotNull,
        'fullyPaid' => $faker->randomDigitNotNull,
        'isAdvancePaymentYN' => $faker->randomDigitNotNull,
        'dueDate' => $faker->date('Y-m-d H:i:s'),
        'LCPaymentYN' => $faker->randomDigitNotNull,
        'requestedByEmpID' => $faker->word,
        'requestedByEmpName' => $faker->word,
        'reqAmountInPOTransCur' => $faker->randomDigitNotNull,
        'reqAmountInPOLocalCur' => $faker->randomDigitNotNull,
        'reqAmountInPORptCur' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
