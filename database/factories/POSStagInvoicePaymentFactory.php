<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSStagInvoicePayment;
use Faker\Generator as Faker;

$factory->define(POSStagInvoicePayment::class, function (Faker $faker) {

    return [
        'invoiceID' => $faker->randomDigitNotNull,
        'paymentConfigMasterID' => $faker->randomDigitNotNull,
        'paymentConfigDetailID' => $faker->randomDigitNotNull,
        'glAccountType' => $faker->randomDigitNotNull,
        'GLCode' => $faker->randomDigitNotNull,
        'amount' => $faker->randomDigitNotNull,
        'reference' => $faker->word,
        'customerAutoID' => $faker->randomDigitNotNull,
        'isAdvancePayment' => $faker->randomDigitNotNull,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdUserName' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedUserName' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'transaction_log_id' => $faker->randomDigitNotNull
    ];
});
