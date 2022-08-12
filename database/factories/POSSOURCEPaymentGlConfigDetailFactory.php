<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSSOURCEPaymentGlConfigDetail;
use Faker\Generator as Faker;

$factory->define(POSSOURCEPaymentGlConfigDetail::class, function (Faker $faker) {

    return [
        'companyCode' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdPCID' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdUserName' => $faker->word,
        'GLCode' => $faker->randomDigitNotNull,
        'isAuthRequired' => $faker->word,
        'isSync' => $faker->randomDigitNotNull,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedUserName' => $faker->word,
        'paymentConfigMasterID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'transaction_log_id' => $faker->randomDigitNotNull,
        'warehouseID' => $faker->randomDigitNotNull
    ];
});
