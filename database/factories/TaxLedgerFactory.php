<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TaxLedger;
use Faker\Generator as Faker;

$factory->define(TaxLedger::class, function (Faker $faker) {

    return [
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentMasterAutoID' => $faker->randomDigitNotNull,
        'documentCode' => $faker->word,
        'documentDate' => $faker->date('Y-m-d H:i:s'),
        'subCategoryID' => $faker->randomDigitNotNull,
        'masterCategoryID' => $faker->randomDigitNotNull,
        'rcmApplicableYN' => $faker->randomDigitNotNull,
        'localAmount' => $faker->randomDigitNotNull,
        'rptAmount' => $faker->randomDigitNotNull,
        'transAmount' => $faker->randomDigitNotNull,
        'transER' => $faker->randomDigitNotNull,
        'localER' => $faker->randomDigitNotNull,
        'comRptER' => $faker->randomDigitNotNull,
        'localCurrencyID' => $faker->randomDigitNotNull,
        'rptCurrencyID' => $faker->randomDigitNotNull,
        'transCurrencyID' => $faker->randomDigitNotNull,
        'isClaimable' => $faker->randomDigitNotNull,
        'isClaimed' => $faker->randomDigitNotNull,
        'taxAuthorityAutoID' => $faker->randomDigitNotNull,
        'inputVATGlAccountID' => $faker->randomDigitNotNull,
        'inputVatTransferAccountID' => $faker->randomDigitNotNull,
        'outputVatTransferGLAccountID' => $faker->randomDigitNotNull,
        'outputVatGLAccountID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s')
    ];
});
