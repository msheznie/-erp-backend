<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TaxLedgerDetail;
use Faker\Generator as Faker;

$factory->define(TaxLedgerDetail::class, function (Faker $faker) {

    return [
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentMasterAutoID' => $faker->randomDigitNotNull,
        'documentDetailID' => $faker->randomDigitNotNull,
        'taxLedgerID' => $faker->randomDigitNotNull,
        'vatSubCategoryID' => $faker->randomDigitNotNull,
        'vatMasterCategoryID' => $faker->randomDigitNotNull,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'documentDate' => $faker->date('Y-m-d H:i:s'),
        'postedDate' => $faker->date('Y-m-d H:i:s'),
        'documentNumber' => $faker->word,
        'chartOfAccountSystemID' => $faker->randomDigitNotNull,
        'accountCode' => $faker->word,
        'accountDescription' => $faker->text,
        'transactionCurrencyID' => $faker->randomDigitNotNull,
        'originalInvoice' => $faker->word,
        'originalInvoiceDate' => $faker->date('Y-m-d H:i:s'),
        'dateOfSupply' => $faker->date('Y-m-d H:i:s'),
        'partyType' => $faker->randomDigitNotNull,
        'partyAutoID' => $faker->randomDigitNotNull,
        'partyVATRegisteredYN' => $faker->word,
        'partyVATRegNo' => $faker->word,
        'countryID' => $faker->randomDigitNotNull,
        'itemSystemCode' => $faker->randomDigitNotNull,
        'itemCode' => $faker->word,
        'itemDescription' => $faker->text,
        'VATPercentage' => $faker->randomDigitNotNull,
        'taxableAmount' => $faker->randomDigitNotNull,
        'VATAmount' => $faker->randomDigitNotNull,
        'localER' => $faker->randomDigitNotNull,
        'localAmount' => $faker->randomDigitNotNull,
        'reportingER' => $faker->randomDigitNotNull,
        'reportingAmount' => $faker->randomDigitNotNull,
        'taxableAmountLocal' => $faker->randomDigitNotNull,
        'taxableAmountReporting' => $faker->randomDigitNotNull,
        'VATAmountLocal' => $faker->randomDigitNotNull,
        'VATAmountRpt' => $faker->randomDigitNotNull,
        'inputVATGlAccountID' => $faker->randomDigitNotNull,
        'inputVatTransferAccountID' => $faker->randomDigitNotNull,
        'outputVatTransferGLAccountID' => $faker->randomDigitNotNull,
        'outputVatGLAccountID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s')
    ];
});
