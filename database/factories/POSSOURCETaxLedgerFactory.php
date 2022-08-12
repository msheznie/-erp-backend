<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSSOURCETaxLedger;
use Faker\Generator as Faker;

$factory->define(POSSOURCETaxLedger::class, function (Faker $faker) {

    return [
        'amount' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'countryID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdPCID' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdUserName' => $faker->word,
        'documentDetailAutoID' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'documentMasterAutoID' => $faker->randomDigitNotNull,
        'formula' => $faker->text,
        'isClaimable' => $faker->randomDigitNotNull,
        'isClaimed' => $faker->randomDigitNotNull,
        'ismanuallychanged' => $faker->randomDigitNotNull,
        'isSync' => $faker->randomDigitNotNull,
        'locationID' => $faker->randomDigitNotNull,
        'locationType' => $faker->randomDigitNotNull,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedUserName' => $faker->word,
        'outputVatGL' => $faker->word,
        'outputVatTransferGL' => $faker->word,
        'partyID' => $faker->randomDigitNotNull,
        'partyVATEligibleYN' => $faker->randomDigitNotNull,
        'taxDetailAutoID' => $faker->randomDigitNotNull,
        'taxFormulaDetailID' => $faker->randomDigitNotNull,
        'taxFormulaMasterID' => $faker->randomDigitNotNull,
        'taxGlAutoID' => $faker->randomDigitNotNull,
        'taxMasterID' => $faker->randomDigitNotNull,
        'taxPercentage' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'transaction_log_id' => $faker->randomDigitNotNull,
        'transferGLAutoID' => $faker->randomDigitNotNull,
        'vatTypeID' => $faker->randomDigitNotNull
    ];
});
