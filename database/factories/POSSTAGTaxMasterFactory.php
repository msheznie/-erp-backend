<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSSTAGTaxMaster;
use Faker\Generator as Faker;

$factory->define(POSSTAGTaxMaster::class, function (Faker $faker) {

    return [
        'companyCode' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdPCID' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdUserName' => $faker->word,
        'effectiveFrom' => $faker->word,
        'erp_tax_master_id' => $faker->randomDigitNotNull,
        'inputVatGLAccountAutoID' => $faker->randomDigitNotNull,
        'inputVatTransferGLAccountAutoID' => $faker->randomDigitNotNull,
        'isActive' => $faker->word,
        'isClaimable' => $faker->randomDigitNotNull,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedUserName' => $faker->word,
        'outputVatGLAccountAutoID' => $faker->randomDigitNotNull,
        'outputVatTransferGLAccountAutoID' => $faker->randomDigitNotNull,
        'supplierGLAutoID' => $faker->randomDigitNotNull,
        'taxCategory' => $faker->randomDigitNotNull,
        'taxDescription' => $faker->word,
        'taxPercentage' => $faker->randomDigitNotNull,
        'taxReferenceNo' => $faker->word,
        'taxShortCode' => $faker->word,
        'taxType' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'transaction_log_id' => $faker->randomDigitNotNull
    ];
});
