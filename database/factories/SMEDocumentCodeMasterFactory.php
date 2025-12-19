<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMEDocumentCodeMaster;
use Faker\Generator as Faker;

$factory->define(SMEDocumentCodeMaster::class, function (Faker $faker) {

    return [
        'documentID' => $faker->word,
        'document' => $faker->word,
        'prefix' => $faker->word,
        'startSerialNo' => $faker->randomDigitNotNull,
        'serialNo' => $faker->randomDigitNotNull,
        'formatLength' => $faker->randomDigitNotNull,
        'approvalLevel' => $faker->randomDigitNotNull,
        'approvalSignatureLevel' => $faker->randomDigitNotNull,
        'format_1' => $faker->word,
        'format_2' => $faker->word,
        'format_3' => $faker->word,
        'format_4' => $faker->word,
        'format_5' => $faker->word,
        'format_6' => $faker->word,
        'isPushNotifyEnabled' => $faker->randomDigitNotNull,
        'isFYBasedSerialNo' => $faker->randomDigitNotNull,
        'postDate' => $faker->randomDigitNotNull,
        'printHeaderFooterYN' => $faker->randomDigitNotNull,
        'printFooterYN' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
