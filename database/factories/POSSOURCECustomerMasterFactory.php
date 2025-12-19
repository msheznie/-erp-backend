<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSSOURCECustomerMaster;
use Faker\Generator as Faker;

$factory->define(POSSOURCECustomerMaster::class, function (Faker $faker) {

    return [
        'capAmount' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdPCID' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdUserName' => $faker->word,
        'customerAddress1' => $faker->word,
        'customerAddress2' => $faker->word,
        'customerCountry' => $faker->word,
        'customerCountryID' => $faker->randomDigitNotNull,
        'customerCreditLimit' => $faker->randomDigitNotNull,
        'customerCreditPeriod' => $faker->randomDigitNotNull,
        'customerCurrency' => $faker->word,
        'customerCurrencyDecimalPlaces' => $faker->randomDigitNotNull,
        'customerCurrencyID' => $faker->randomDigitNotNull,
        'customerEmail' => $faker->word,
        'customerFax' => $faker->word,
        'customerName' => $faker->word,
        'customerSystemCode' => $faker->word,
        'customerTelephone' => $faker->word,
        'customerUrl' => $faker->word,
        'deleteByEmpID' => $faker->word,
        'deletedDatetime' => $faker->date('Y-m-d H:i:s'),
        'deletedYN' => $faker->randomDigitNotNull,
        'erp_customer_master_id' => $faker->randomDigitNotNull,
        'IdCardNumber' => $faker->word,
        'isActive' => $faker->randomDigitNotNull,
        'isSync' => $faker->randomDigitNotNull,
        'levelNo' => $faker->word,
        'locationID' => $faker->randomDigitNotNull,
        'masterID' => $faker->randomDigitNotNull,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedUserName' => $faker->word,
        'partyCategoryID' => $faker->randomDigitNotNull,
        'secondaryCode' => $faker->word,
        'taxGroupID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'transaction_log_id' => $faker->randomDigitNotNull,
        'vatEligible' => $faker->randomDigitNotNull,
        'vatIdNo' => $faker->word,
        'vatNumber' => $faker->randomDigitNotNull,
        'vatPercentage' => $faker->randomDigitNotNull
    ];
});
